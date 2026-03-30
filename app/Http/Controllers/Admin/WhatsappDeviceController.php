<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappDevice;
use App\Models\Store;
use App\Services\GowaService;
use Illuminate\Http\Request;

class WhatsappDeviceController extends Controller
{
    protected GowaService $gowaService;

    public function __construct(GowaService $gowaService)
    {
        $this->gowaService = $gowaService;
    }
    /**
     * Display a listing of WhatsApp devices
     */
    public function index(Request $request)
    {
        $query = WhatsappDevice::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('is_logged_in')) {
            $query->where('is_logged_in', $request->boolean('is_logged_in'));
        }

        $devices = $query->orderBy('created_at', 'desc')->paginate(20);
        $statuses = WhatsappDevice::distinct()->pluck('status');

        return view('admin.devices.index', compact('devices', 'statuses'));
    }

    /**
     * Show device details
     */
    public function show(WhatsappDevice $device)
    {
        $device->load(['store', 'messages' => function($query) {
            $query->latest()->take(10);
        }]);

        return view('admin.devices.show', compact('device'));
    }

    /**
     * Sync devices from GoWA API
     */
    public function sync()
    {
        $result = $this->gowaService->getDevices();

        if (!$result['success']) {
            return redirect()->route('admin.devices.index')
                ->with('error', $result['message']);
        }

        $devices = $result['data']; // ambil data dari API

        // Get all device_ids from GoWA
        $gowaDeviceIds = collect($devices)->pluck('id')->toArray();

        // Get all device_ids currently in database
        $dbDeviceIds = WhatsappDevice::pluck('device_id')->toArray();

        // Find devices to delete (in DB but not in GoWA)
        $devicesToDelete = array_diff($dbDeviceIds, $gowaDeviceIds);

        // Delete devices not present in GoWA
        if (!empty($devicesToDelete)) {
            // Optional: Unlink from stores first
            Store::whereIn('whatsapp_device_id', $devicesToDelete)
                ->update(['whatsapp_device_id' => null]);

            WhatsappDevice::whereIn('device_id', $devicesToDelete)->delete();
        }

        // Update or create devices from GoWA
        foreach ($devices as $device) {
            $existing = WhatsappDevice::where('device_id', $device['id'])->first();

            $data = [
                'name' => $device['display_name'] ?? $device['id'],
                'status' => $device['state'] ?? 'unknown',
                'phone_number' => isset($device['jid']) 
                    ? explode('@', $device['jid'])[0] 
                    : null,
                'is_logged_in' => ($device['state'] ?? '') === 'logged_in',
                'device_info' => json_encode($device),
            ];

            if ($existing) {
                // ✅ UPDATE
                $existing->update($data);
            } else {
                // ✅ CREATE
                WhatsappDevice::create(array_merge(
                    ['device_id' => $device['id']],
                    $data
                ));
            }
        }

        return redirect()->route('admin.devices.index')
            ->with('success', 'Devices synced successfully.');
    }

    /**
     * Show create device form
     */
    public function create()
    {
        return view('admin.devices.create');
    }

    /**
     * Store new device
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'device_id' => 'required|string|max:255|unique:whatsapp_devices,device_id',
        ]);

        $result = $this->gowaService->createDevice(
            $request->device_id,
            $request->name
        );

        if ($result['success']) {
            return redirect()->route('admin.devices.index')
                ->with('success', 'Device created successfully.');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $result['message']);
    }

    /**
     * Show login QR for device
     */
    public function login(WhatsappDevice $device)
    {
        $result = $this->gowaService->getDeviceLoginQr($device->device_id);

        if ($result['success']) {
            return view('admin.devices.login', [
                'device' => $device,
                'qrData' => $result['data']
            ]);
        }

        return redirect()->route('admin.devices.show', $device)
            ->with('error', $result['message']);
    }

    /**
     * Link device to store
     */
    public function linkToStore(Request $request, WhatsappDevice $device)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
        ]);

        $store = Store::find($request->store_id);
        $store->update(['whatsapp_device_id' => $device->device_id]);

        return redirect()->route('admin.devices.show', $device)
            ->with('success', 'Device linked to store successfully.');
    }

    /**
     * Unlink device from store
     */
    public function unlinkFromStore(WhatsappDevice $device)
    {
        $store = Store::where('whatsapp_device_id', $device->device_id)->first();
        if ($store) {
            $store->update(['whatsapp_device_id' => null]);
        }

        return redirect()->route('admin.devices.show', $device)
            ->with('success', 'Device unlinked from store successfully.');
    }

    /**
     * Delete device
     */
    public function destroy(WhatsappDevice $device)
    {
        // Check if device is linked to a store
        $store = Store::where('whatsapp_device_id', $device->device_id)->first();
        if ($store) {
            return redirect()->route('admin.devices.show', $device)
                ->with('error', 'Cannot delete device that is linked to a store. Unlink it first.');
        }

        $device->delete();

        return redirect()->route('admin.devices.index')
            ->with('success', 'Device deleted successfully.');
    }
}
