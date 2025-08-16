<thead class="bg-gray-50 <?php echo e($headerClass); ?>">
    <tr>
        <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $columnKey = is_array($column) ? ($column['key'] ?? '') : $column;
                $columnLabel = is_array($column) ? ($column['label'] ?? $column['title'] ?? $columnKey) : $column;
                $columnSortable = is_array($column) ? ($column['sortable'] ?? true) : true;
                $columnClass = is_array($column) ? ($column['class'] ?? '') : '';
                $columnWidth = is_array($column) ? ($column['width'] ?? '') : '';
                $columnAlign = is_array($column) ? ($column['align'] ?? 'left') : 'left';
            ?>
            
            <th scope="col" 
                class="px-6 py-3 text-<?php echo e($columnAlign); ?> text-xs font-medium text-gray-500 uppercase tracking-wider <?php echo e($columnClass); ?>"
                <?php if($columnWidth): ?> style="width: <?php echo e($columnWidth); ?>" <?php endif; ?>>
                
                <?php if($sortable && $columnSortable && $columnKey): ?>
                    <a href="<?php echo e($this->getSortUrl($columnKey)); ?>" 
                       class="group inline-flex items-center space-x-1 text-gray-500 hover:text-gray-700">
                        <span><?php echo e($columnLabel); ?></span>
                        <span class="ml-2 flex-none rounded text-gray-400 group-hover:text-gray-500">
                            <?php if($this->isCurrentSort($columnKey)): ?>
                                <?php if($currentDirection === 'asc'): ?>
                                    <svg class="h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M14.77 12.79a.75.75 0 01-1.06-.02L10 8.832 6.29 12.77a.75.75 0 11-1.08-1.04l4.25-4.5a.75.75 0 011.08 0l4.25 4.5a.75.75 0 01-.02 1.06z" clip-rule="evenodd" />
                                    </svg>
                                <?php else: ?>
                                    <svg class="h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                <?php endif; ?>
                            <?php else: ?>
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z" />
                                </svg>
                            <?php endif; ?>
                        </span>
                    </a>
                <?php else: ?>
                    <?php echo e($columnLabel); ?>

                <?php endif; ?>
                
                <?php if(is_array($column) && isset($column['description'])): ?>
                    <div class="text-xs text-gray-400 font-normal mt-1"><?php echo e($column['description']); ?></div>
                <?php endif; ?>
            </th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        
        <?php if($actions): ?>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                <?php echo e($actionColumnTitle); ?>

            </th>
        <?php endif; ?>
    </tr>
</thead>
<?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views\components\table-header.blade.php ENDPATH**/ ?>