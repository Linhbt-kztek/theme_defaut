<?php if(!empty($breadcrumb) || !empty($create_btn)): ?>
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <div class="page-title-left">
            <ol class="breadcrumb m-0">
                <?php if(!empty($breadcrumb)): ?>
                    <?php $__currentLoopData = $breadcrumb; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="breadcrumb-item"><a
                                href="<?php echo e(!empty($val['route']) ? ($val['route'] == 'category.index' ? route($val['route'], ['type' => $type]) : route($val['route'])) : '#'); ?>">
                                <b><?php echo e($val['title']); ?></b>
                            </a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </ol>
        </div>
        <div class="page-title-right">
            <!-- btn create -->
            <?php if(!empty($create_btn)): ?>
                <!-- neu là mảng -->
                <?php if(!empty($create_btn[0])): ?>
                    <?php $__currentLoopData = $create_btn; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="float: right;margin-right: 5px;">
                            <a id="<?php echo e(!empty($item['id']) ? $item['id'] : ''); ?>"
                                class="btn <?php echo e(!empty($item['btn-sm']) ? 'btn-sm' : ''); ?> btn-<?php echo e($item['class'] ?? 'primary'); ?>"
                                <?php echo e($item['type']
                                    ? 'href=' .
                                        ($item['link'] ??
                                            route($item['route']) .
                                                (!empty($item['query_params']) ? '?' . http_build_query($item['query_params']) : ''))
                                    : 'onclick=' . $item['function']); ?>>
                                <i
                                    class="<?php echo e($item['icon'] ?? 'ri-add-line'); ?> align-bottom me-1"></i><?php echo e($item['name'] ?? 'Thêm mới'); ?>

                            </a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <!-- chỉ có 1 phan tu -->
                    <div style="float: right">
                        <a id="<?php echo e(!empty($item['id']) ? $item['id'] : ''); ?>"
                            class="btn <?php echo e(!empty($create_btn['btn-sm']) ? 'btn-sm' : ''); ?> btn-<?php echo e($create_btn['class'] ?? 'primary'); ?>"
                            <?php echo e($create_btn['type'] ? 'href=' . ($create_btn['link'] ?? route($create_btn['route'])) : 'onclick=' . $create_btn['function']); ?>>
                            <i
                                class="<?php echo e($create_btn['icon'] ?? 'ri-add-line'); ?> align-bottom me-1"></i><?php echo e($create_btn['name'] ?? 'Thêm mới'); ?>

                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <!-- btn create -->

            <!-- btn excel -->
            <?php if(!empty($create_excel_btn)): ?>
                <div style="float: right;margin-right:5px">
                    <form class="float-end" id="excel-form" action="<?php echo e(route($create_excel_btn['route'])); ?>"
                        method="post" style="float: right"
                        target="<?php echo e(!empty($create_excel_btn['target']) ? $create_excel_btn['target'] : '_blank'); ?>">
                        <?php echo csrf_field(); ?>
                        <input hidden="hidden" name="is_export" value="1">
                        <?php if(!empty($create_excel_btn['input_hidden'])): ?>
                            <?php $__currentLoopData = $create_excel_btn['input_hidden']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <input type="hidden" name="<?php echo e($item['name']); ?>" id="<?php echo e($item['id']); ?>"
                                    value="<?php echo e($item['value']); ?>">
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <button
                            class="btn <?php echo e(!empty($create_excel_btn['btn-sm']) ? 'btn-sm' : ''); ?> btn-<?php echo e($create_excel_btn['class'] ?? 'primary'); ?>"
                            id="excel-btn">
                            <i class="<?php echo e($create_btn['icon'] ?? 'ri-add-line'); ?> align-bottom me-1"></i>Excel
                            
                        </button>
                    </form>
                </div>
            <?php endif; ?>
            <!-- btn excel -->


        </div>
    </div>
<?php endif; ?>
<style>
    .breadcrumb-item {
        font-size: 14px;
    }

    .breadcrumb-item .breadcrumb-item:before {
        font-size: 20px;
    }
</style>
<?php /**PATH C:\laragon\www\theme_defaut\resources\views/components/breadcrumb.blade.php ENDPATH**/ ?>