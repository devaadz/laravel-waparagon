$form = App\Models\Form::first();
echo 'UUID: ' . $form->id . PHP_EOL;
echo 'Name: ' . $form->name . PHP_EOL;