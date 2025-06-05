<?php
defined('B_PROLOG_INCLUDED') || die;
?>

<span class="fields string field-wrap">
	<? foreach ($arResult['PREPARED_VALUES'] as $item) { ?>
        <span class="fields string field-item">
			<? if (!empty($item['LINK'])) { ?>
                <a href="<?= htmlspecialcharsbx($item['LINK']) ?>">
					<?= $item['FORMATTED_VALUE'] ?>
				</a>
            <? } else { ?>
                <?= $item['FORMATTED_VALUE'] ?>
            <? } ?>
		</span>
    <? } ?>
</span>