<?= "<?php\n"; ?>

<?= "namespace {$module};\n"; ?>

class <?= $codeName; ?> extends \Inji\Model {
//modelParams<?php
if (!empty($name)) {
    echo "\n    static " . '$objectName = \'' . $name . "';";
}
if (!empty($labels)) {
    echo "\n    static " . '$labels = [' . CodeGenerator::genArray($labels, 1) . "\n    ];";
}
if (!empty($cols)) {
    echo "\n    static " . '$cols = [' . CodeGenerator::genArray($cols, 1) . "\n    ];";
}
?>
//!modelParams
//modelBody
//!modelBody
}
