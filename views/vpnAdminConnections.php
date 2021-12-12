<?php declare(strict_types=1); ?>
<?php /** @var \Vpn\Portal\Tpl $this */?>
<?php /** @var array<string,array<array{user_id:string,connection_id:string,display_name:string,ip_list:array<string>}>> $profileConnectionList */?>
<?php /** @var array<string,string> $idNameMapping */?>
<?php /** @var string $requestRoot */?>

<?php $this->layout('base', ['activeItem' => 'connections', 'pageTitle' => $this->t('Connections')]); ?>
<?php $this->start('content'); ?>
    <table class="tbl">
        <thead>
            <tr>
                <th><?=$this->t('Profile'); ?></th>
                <th><?=$this->t('#Active Connections'); ?></th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($profileConnectionList as $profileId => $connectionList): ?>
        <tr>
            <td><a title="<?=$this->e($profileId); ?>" href="#<?=$this->e($profileId); ?>"><?=$this->e($idNameMapping[$profileId]); ?></a></td>
            <td><?=count($connectionList); ?></td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
<?php foreach ($profileConnectionList as $profileId => $connectionList): ?>
        <h2 id="<?=$this->e($profileId); ?>"><?=$this->e($idNameMapping[$profileId]); ?></h2>
        <?php if (0 === count($connectionList)): ?>
            <p class="plain"><?=$this->t('No clients connected.'); ?></p>
        <?php else: ?>
            <table class="tbl">
            <thead>
                <tr>
                    <th><?=$this->t('User ID'); ?></th>
                    <th><?=$this->t('Name'); ?></th>
                    <th><?=$this->t('IP Address'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($connectionList as $connection): ?>
                <tr>
                    <td>
                        <a href="<?=$this->e($requestRoot); ?>user?user_id=<?=$this->e($connection['user_id'], 'rawurlencode'); ?>" title="<?=$this->e($connection['user_id']); ?>"><?=$this->etr($connection['user_id'], 25); ?></a>
                    </td>
                    <td>
                        <span title="<?=$this->e($connection['display_name']); ?>"><?=$this->etr($connection['display_name'], 25); ?></span>
                    </td>
                    <td>
                        <ul>
                            <?php foreach ($connection['ip_list'] as $ip): ?>
                            <li><code><?=$this->e($ip); ?></code></li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            </table>
        <?php endif; ?>
<?php endforeach; ?>
<?php $this->stop('content'); ?>
