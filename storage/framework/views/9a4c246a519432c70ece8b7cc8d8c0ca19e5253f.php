<?php
$route_group = Request::route()->getName();
$array_route_group = explode('.', $route_group);
$count = 1;
?>
<?php $__currentLoopData = config('menus'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $count++; ?>
    <!-- nếu có menu con -->
    <?php if(!empty($item['child_menu'])): ?>
        <?php echo $__env->make('components.child-menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>
        <!-- nếu menu con rỗng -->
        <li class="nav-item">
            
            <a class="nav-link menu-link <?php echo e(strpos($route_group, $item['route_group']) === 0 ? 'active' : ''); ?>"
                href="<?php echo e(route($item['route'])); ?>">
                <span>
                    <i class="<?php echo e($item['class']); ?>"> </i> <?php echo e($k); ?>

                </span>
            </a>
        </li>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php /**PATH C:\laragon\www\theme_defaut\resources\views/components/left-menu.blade.php ENDPATH**/ ?>