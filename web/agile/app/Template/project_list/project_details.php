<div class="table-list-details table-list-details-with-icons">
    <ul>

        <?php if ($project['owner_id'] > 0): ?>
            <li><?php
                    print_r($project['owner_name']); ?></li>
        <?php endif ?>

        <?php if ($project['start_date']): ?>
            <li><?= t('Start date:') . ' ' . $this->dt->date($project['start_date']) ?></li>
        <?php endif ?>

        <?php if ($project['end_date']): ?>
            <li><?= t('End date:') . ' ' . $this->dt->date($project['end_date']) ?></li>
        <?php endif ?>
    </ul>
</div>