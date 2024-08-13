<form
    method="<?= $config["config"]["method"]??"GET" ?>"
    action="<?= $config["config"]["action"]??"" ?>"
    class="<?= $config["config"]["class"]??"" ?>"
    id="<?= $config["config"]["id"]??"" ?>">


    <?php foreach ($config["inputs"] as $name => $configInput): ?>
        <div class="input-form">
            <?php if ($configInput["type"] == "hidden"): ?>
                <input
                    type="hidden"
                    name="<?= $name ?>"
                    id="<?= $configInput["id"] ?? " " ?>"
                    class="<?= $configInput["class"] ?? "" ?>"
                    placeholder="<?= $configInput["placeholder"] ?? "" ?>"
                    value="<?= htmlspecialchars($configInput["value"] ?? '') ?>"
                    <?= (!empty($configInput["required"])) ? "required" : "" ?>
                >
            <?php elseif ($configInput["type"] == "radio"): ?>
                <label for="<?= $configInput['id'] ?? '' ?>"><?= htmlspecialchars($name) ?></label><br>

                <?php foreach ($configInput["options"] as $optionValue => $optionText):
                    // Détermine si l'option doit être cochée en comparant avec la 'value' de l'input

                    $isChecked = ($configInput['value'] == $optionValue) ? 'checked' : '';
                    ?>
                    <input
                        type="radio"
                        id="<?= htmlspecialchars($optionValue) ?>"
                        name="<?= htmlspecialchars($configInput['name']) ?>"
                        value="<?= htmlspecialchars($optionValue) ?>"
                        <?= $isChecked ?>
                        class="<?= $configInput["class"] ?? "input-form" ?>"
                    >
                    <label for="<?= htmlspecialchars($optionValue) ?>"><?= htmlspecialchars($optionText) ?></label><br>
                <?php endforeach; ?>

            <?php else: ?>
                <label for="<?= $configInput['id'] ?? '' ?>"><?= htmlspecialchars($name) ?></label>
                <input
                    name="<?= $name ?>"
                    type="<?= $configInput["type"] ?? "text" ?>"
                    id="<?= $configInput["id"] ?? " " ?>"
                    class="<?= $configInput["class"] ?? "" ?>"
                    placeholder="<?= $configInput["placeholder"] ?? "" ?>"
                    <?= (!empty($configInput["required"])) ? "required" : "" ?>
                    value="<?= htmlspecialchars($configInput["value"] ?? '') ?>"
                ><br>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <?php // generer les textareas si elles existent dans les configs
    if (!empty($config["textareas"])) {
        foreach ($config["textareas"] as $name => $configTextarea):
            ?>
            <div class="input-form">
                <label for="<?= $configTextarea['id'] ?? '' ?>"><?= htmlspecialchars($name) ?></label>
                <textarea
                    name="<?= $name ?>"
                    id="<?= $configTextarea["id"] ?? "" ?>"
                    class="<?= $configTextarea["class"] ?? "" ?>"
                    placeholder="<?= $configTextarea["placeholder"] ?? "" ?>"
                    <?= (!empty($configTextarea["required"])) ? "required" : "" ?>>
                    <?= htmlspecialchars($configTextarea["value"] ?? '') ?>
                </textarea><br>
            </div>
        <?php
        endforeach;
    }
    ?>

    <input type="submit" class="<?= $submitButtonClass??"" ?>" value="<?= $config["config"]["submit"]??"Envoyer" ?>" >

    <?php if(!empty($dataError)) :?>
        <div class="error">
            <?php foreach ($dataError as $error):?>
                <li><?= $error ?></li>
            <?php endforeach;?>
        </div>
    <?php endif;?>

    <?php if(!empty($dataSuccess)) :?>
        <div class="success">
            <?php foreach ($dataSuccess as $success):?>
                <li><?= $success ?></li>
            <?php endforeach;?>
        </div>
    <?php endif;?>

</form>
