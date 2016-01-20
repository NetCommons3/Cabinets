<?php
echo $this->NetCommonsForm->create(false, ['type' => 'file']);
//echo $this->NetCommonsForm->create(null, ['type' => 'file']);
echo $this->NetCommonsForm->input('import_csv', ['type' => 'file']);
?>
<?php
echo $this->NetCommonsForm->submit();
echo $this->NetCommonsForm->end();
