    <?php
    $active = false;
    $show_nav_item = false;
?>
<?php $__currentLoopData = $item['child_menu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $keyVal => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if(!empty($val['child_menu'])): ?>
        <?php $__currentLoopData = $val['child_menu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key_val => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                if (in_array($value['route_group'], $array_route_group)) {
                    # code...
                    $active = true;
                }
            ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
    <?php
        if (in_array($val['route_group'], $array_route_group)) {
            # code...
            $active = true;
        }
    ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check(@$val['permission'])): ?>
        <?php ($show_nav_item = true); ?>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<!-- nếu bât cứ menu cấp 2 mà có quyền thì mới hiện menu cấp 1 -->
<?php if($show_nav_item): ?>
    <li class="nav-item">
        <!-- hiện menu cấp 1 -->
        <a class="nav-link <?php echo e($active ? 'active' : 'collapse'); ?>" href="#sidebarDashboards<?php echo e($count); ?>"
            data-bs-toggle="collapse" aria-expanded="<?php echo e($active ? 'true' : 'false'); ?>" aria-controls="sidebarDashboards"
            style="font-size: .875rem !important; background-color: #0f1c2f00 !important;">
            <span>
                <i class="<?php echo e($item['class']); ?>"></i>
                <?php echo e($k); ?> <!-- tên menu -->
            </span>
        </a>
        <!-- hiện menu cấp 1 -->

        <!-- hiện menu cấp 2 -->
        <div class="menu-dropdown <?php echo e($active ? '' : 'collapse'); ?>" id="sidebarDashboards<?php echo e($count); ?>"
            style="">
            <ul class="nav nav-sm flex-column">
                <?php $__currentLoopData = $item['child_menu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $keyVal => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(!empty($val['child_menu'])): ?>
                        <?php $count++; ?>
                        <?php echo $__env->make('components.child-menu1', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php else: ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check(@$val['permission'], 'web')): ?>
                            <li class="nav-item">
                                <a href="<?php echo e(route($val['route'])); ?>"
                                    class="nav-link <?php echo e(strpos($route_group, $val['route_group']) === 0 ? 'active' : ''); ?>"
                                    style="font-size: .875rem !important; background-color: #0f1c2f00 !important;">
                                    <?php if(!empty($val['class'])): ?>
                                        <i class="<?php echo e($val['class']); ?>">
                                        </i>
                                    <?php endif; ?>
                                    <?php echo e($keyVal); ?>

                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <!-- hiện menu cấp 2 -->
    </li>
<?php endif; ?>
<?php /**PATH C:\laragon\www\theme_defaut\resources\views/components/child-menu.blade.php ENDPATH**/ ?>