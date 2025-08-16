

<?php $__env->startSection('title', 'Equipment'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginal9818d3adea8b44b535ac9eb7d462bdc4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9818d3adea8b44b535ac9eb7d462bdc4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-header','data' => ['title' => 'Equipment Management']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Equipment Management']); ?>
     <?php $__env->slot('actions', null, []); ?> 
        <?php if (isset($component)) { $__componentOriginal363b70c2a19d965f2639dd78c52407ba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal363b70c2a19d965f2639dd78c52407ba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-button','data' => ['variant' => 'primary','dataAction' => 'open-add-modal','icon' => 'plus']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','data-action' => 'open-add-modal','icon' => 'plus']); ?>
            Add Equipment
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal363b70c2a19d965f2639dd78c52407ba)): ?>
<?php $attributes = $__attributesOriginal363b70c2a19d965f2639dd78c52407ba; ?>
<?php unset($__attributesOriginal363b70c2a19d965f2639dd78c52407ba); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal363b70c2a19d965f2639dd78c52407ba)): ?>
<?php $component = $__componentOriginal363b70c2a19d965f2639dd78c52407ba; ?>
<?php unset($__componentOriginal363b70c2a19d965f2639dd78c52407ba); ?>
<?php endif; ?>
     <?php $__env->endSlot(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9818d3adea8b44b535ac9eb7d462bdc4)): ?>
<?php $attributes = $__attributesOriginal9818d3adea8b44b535ac9eb7d462bdc4; ?>
<?php unset($__attributesOriginal9818d3adea8b44b535ac9eb7d462bdc4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9818d3adea8b44b535ac9eb7d462bdc4)): ?>
<?php $component = $__componentOriginal9818d3adea8b44b535ac9eb7d462bdc4; ?>
<?php unset($__componentOriginal9818d3adea8b44b535ac9eb7d462bdc4); ?>
<?php endif; ?>

<div class="space-y-6">
    <!-- Equipment Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php if (isset($component)) { $__componentOriginal2c67742b68990e2b8b4f34efc673311d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2c67742b68990e2b8b4f34efc673311d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-card','data' => ['title' => 'Available Equipment','class' => 'border-l-4 border-green-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Available Equipment','class' => 'border-l-4 border-green-500']); ?>
            <div class="text-3xl font-bold text-green-600">
                <?php echo e($equipment->where('status', 'available')->count()); ?>

            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2c67742b68990e2b8b4f34efc673311d)): ?>
<?php $attributes = $__attributesOriginal2c67742b68990e2b8b4f34efc673311d; ?>
<?php unset($__attributesOriginal2c67742b68990e2b8b4f34efc673311d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2c67742b68990e2b8b4f34efc673311d)): ?>
<?php $component = $__componentOriginal2c67742b68990e2b8b4f34efc673311d; ?>
<?php unset($__componentOriginal2c67742b68990e2b8b4f34efc673311d); ?>
<?php endif; ?>
        
        <?php if (isset($component)) { $__componentOriginal2c67742b68990e2b8b4f34efc673311d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2c67742b68990e2b8b4f34efc673311d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-card','data' => ['title' => 'Borrowed Equipment','class' => 'border-l-4 border-yellow-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Borrowed Equipment','class' => 'border-l-4 border-yellow-500']); ?>
            <div class="text-3xl font-bold text-yellow-600">
                <?php echo e($equipment->where('status', 'borrowed')->count()); ?>

            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2c67742b68990e2b8b4f34efc673311d)): ?>
<?php $attributes = $__attributesOriginal2c67742b68990e2b8b4f34efc673311d; ?>
<?php unset($__attributesOriginal2c67742b68990e2b8b4f34efc673311d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2c67742b68990e2b8b4f34efc673311d)): ?>
<?php $component = $__componentOriginal2c67742b68990e2b8b4f34efc673311d; ?>
<?php unset($__componentOriginal2c67742b68990e2b8b4f34efc673311d); ?>
<?php endif; ?>
        
        <?php if (isset($component)) { $__componentOriginal2c67742b68990e2b8b4f34efc673311d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2c67742b68990e2b8b4f34efc673311d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-card','data' => ['title' => 'Unavailable Equipment','class' => 'border-l-4 border-red-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Unavailable Equipment','class' => 'border-l-4 border-red-500']); ?>
            <div class="text-3xl font-bold text-red-600">
                <?php echo e($equipment->where('status', 'unavailable')->count()); ?>

            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2c67742b68990e2b8b4f34efc673311d)): ?>
<?php $attributes = $__attributesOriginal2c67742b68990e2b8b4f34efc673311d; ?>
<?php unset($__attributesOriginal2c67742b68990e2b8b4f34efc673311d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2c67742b68990e2b8b4f34efc673311d)): ?>
<?php $component = $__componentOriginal2c67742b68990e2b8b4f34efc673311d; ?>
<?php unset($__componentOriginal2c67742b68990e2b8b4f34efc673311d); ?>
<?php endif; ?>
    </div>

    <!-- Equipment List -->
    <?php if (isset($component)) { $__componentOriginal2c67742b68990e2b8b4f34efc673311d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2c67742b68990e2b8b4f34efc673311d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-card','data' => ['title' => 'Equipment List']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Equipment List']); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-4 mb-6">
                <div class="flex-1">
                    <input type="text" id="search" placeholder="Search equipment..." 
                        value="<?php echo e(request('search')); ?>"
                        class="input-primary">
                </div>
                <select id="status-filter" class="input-primary">
                    <option value="">All Status</option>
                    <option value="available" <?php echo e(request('status') === 'available' ? 'selected' : ''); ?>>Available</option>
                    <option value="borrowed" <?php echo e(request('status') === 'borrowed' ? 'selected' : ''); ?>>Borrowed</option>
                    <option value="unavailable" <?php echo e(request('status') === 'unavailable' ? 'selected' : ''); ?>>Unavailable</option>
                </select>
            </div>
         <?php $__env->endSlot(); ?>

        <?php if (isset($component)) { $__componentOriginal5b6e4ddf77bb62ef7f9d59b743778e3d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5b6e4ddf77bb62ef7f9d59b743778e3d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-table','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
             <?php $__env->slot('header', null, []); ?> 
                <th class="table-header">ID Number</th>
                <th class="table-header">Equipment Type</th>
                <th class="table-header">RFID Tag</th>
                <th class="table-header">Status</th>
                <th class="table-header">Location</th>
                <th class="table-header">Current Borrower</th>
                <th class="table-header text-right">Actions</th>
             <?php $__env->endSlot(); ?>

            <?php $__empty_1 = true; $__currentLoopData = $equipment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="table-row">
                <td class="table-cell">
                    <div class="text-sm font-medium text-gray-900"><?php echo e($item->name); ?></div>
                    <div class="text-sm text-gray-500"><?php echo e(Str::limit($item->description, 50)); ?></div>
                </td>
                <td class="table-cell">
                    <?php echo e($item->category->name); ?>

                </td>
                <td class="table-cell">
                    <?php echo e($item->rfid_tag ?? 'Not Set'); ?>

                </td>
                <td class="table-cell">
                    <?php if (isset($component)) { $__componentOriginal8860cf004fec956b6e41d036eb967550 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8860cf004fec956b6e41d036eb967550 = $attributes; } ?>
<?php $component = App\View\Components\StatusBadge::resolve(['status' => $item->status,'type' => 'equipment'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\StatusBadge::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8860cf004fec956b6e41d036eb967550)): ?>
<?php $attributes = $__attributesOriginal8860cf004fec956b6e41d036eb967550; ?>
<?php unset($__attributesOriginal8860cf004fec956b6e41d036eb967550); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8860cf004fec956b6e41d036eb967550)): ?>
<?php $component = $__componentOriginal8860cf004fec956b6e41d036eb967550; ?>
<?php unset($__componentOriginal8860cf004fec956b6e41d036eb967550); ?>
<?php endif; ?>
                </td>
                <td class="table-cell">
                    <?php echo e($item->location ?? 'Not Set'); ?>

                </td>
                <td class="table-cell">
                    <?php echo e($item->currentBorrower ? $item->currentBorrower->name : 'None'); ?>

                </td>
                <td class="table-cell text-right">
                    <?php if (isset($component)) { $__componentOriginalf172b139bfb09a0149e87401446f3f11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf172b139bfb09a0149e87401446f3f11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-action-group','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-action-group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                        <?php if (isset($component)) { $__componentOriginal363b70c2a19d965f2639dd78c52407ba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal363b70c2a19d965f2639dd78c52407ba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-button','data' => ['variant' => 'secondary','size' => 'sm','dataAction' => 'edit-equipment','dataEquipmentId' => ''.e($item->id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','size' => 'sm','data-action' => 'edit-equipment','data-equipment-id' => ''.e($item->id).'']); ?>
                            Edit
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal363b70c2a19d965f2639dd78c52407ba)): ?>
<?php $attributes = $__attributesOriginal363b70c2a19d965f2639dd78c52407ba; ?>
<?php unset($__attributesOriginal363b70c2a19d965f2639dd78c52407ba); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal363b70c2a19d965f2639dd78c52407ba)): ?>
<?php $component = $__componentOriginal363b70c2a19d965f2639dd78c52407ba; ?>
<?php unset($__componentOriginal363b70c2a19d965f2639dd78c52407ba); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginal363b70c2a19d965f2639dd78c52407ba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal363b70c2a19d965f2639dd78c52407ba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-button','data' => ['variant' => 'danger','size' => 'sm','dataAction' => 'delete-equipment','dataEquipmentId' => ''.e($item->id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'danger','size' => 'sm','data-action' => 'delete-equipment','data-equipment-id' => ''.e($item->id).'']); ?>
                            Delete
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal363b70c2a19d965f2639dd78c52407ba)): ?>
<?php $attributes = $__attributesOriginal363b70c2a19d965f2639dd78c52407ba; ?>
<?php unset($__attributesOriginal363b70c2a19d965f2639dd78c52407ba); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal363b70c2a19d965f2639dd78c52407ba)): ?>
<?php $component = $__componentOriginal363b70c2a19d965f2639dd78c52407ba; ?>
<?php unset($__componentOriginal363b70c2a19d965f2639dd78c52407ba); ?>
<?php endif; ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf172b139bfb09a0149e87401446f3f11)): ?>
<?php $attributes = $__attributesOriginalf172b139bfb09a0149e87401446f3f11; ?>
<?php unset($__attributesOriginalf172b139bfb09a0149e87401446f3f11); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf172b139bfb09a0149e87401446f3f11)): ?>
<?php $component = $__componentOriginalf172b139bfb09a0149e87401446f3f11; ?>
<?php unset($__componentOriginalf172b139bfb09a0149e87401446f3f11); ?>
<?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="7" class="table-cell text-center text-gray-500">
                    No equipment found
                </td>
            </tr>
            <?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5b6e4ddf77bb62ef7f9d59b743778e3d)): ?>
<?php $attributes = $__attributesOriginal5b6e4ddf77bb62ef7f9d59b743778e3d; ?>
<?php unset($__attributesOriginal5b6e4ddf77bb62ef7f9d59b743778e3d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5b6e4ddf77bb62ef7f9d59b743778e3d)): ?>
<?php $component = $__componentOriginal5b6e4ddf77bb62ef7f9d59b743778e3d; ?>
<?php unset($__componentOriginal5b6e4ddf77bb62ef7f9d59b743778e3d); ?>
<?php endif; ?>

        <!-- Pagination -->
        <div class="mt-6">
            <?php echo e($equipment->links()); ?>

        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2c67742b68990e2b8b4f34efc673311d)): ?>
<?php $attributes = $__attributesOriginal2c67742b68990e2b8b4f34efc673311d; ?>
<?php unset($__attributesOriginal2c67742b68990e2b8b4f34efc673311d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2c67742b68990e2b8b4f34efc673311d)): ?>
<?php $component = $__componentOriginal2c67742b68990e2b8b4f34efc673311d; ?>
<?php unset($__componentOriginal2c67742b68990e2b8b4f34efc673311d); ?>
<?php endif; ?>
</div>

<!-- Add Equipment Modal -->
<div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Add New Equipment</h3>
            <form id="addForm" action="<?php echo e(route('admin.equipment.store')); ?>" method="POST" class="mt-4">
                <?php echo csrf_field(); ?>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">ID Number</label>
                    <input type="text" name="name" id="name" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="category">Equipment Type</label>
                    <input type="text" name="category" id="category" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="rfid_tag">RFID Tag</label>
                    <input type="text" name="rfid_tag" id="rfid_tag"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="location">Location</label>
                    <input type="text" name="location" id="location"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>                <div class="flex justify-end mt-6">
                    <button type="button" data-action="close-modal" data-target="addModal" class="btn-secondary mr-2">Cancel</button>
                    <button type="submit" class="btn-primary">Add Equipment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Equipment Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Edit Equipment</h3>
            <form id="editForm" method="POST" class="mt-4">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_name">ID Number</label>
                    <input type="text" name="name" id="edit_name" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_description">Description</label>
                    <textarea name="description" id="edit_description" rows="3"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_category">Equipment Type</label>
                    <input type="text" name="category" id="edit_category" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_rfid_tag">RFID Tag</label>
                    <input type="text" name="rfid_tag" id="edit_rfid_tag"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_location">Location</label>
                    <input type="text" name="location" id="edit_location"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_status">Status</label>
                    <select name="status" id="edit_status" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="available">Available</option>
                        <option value="borrowed">Borrowed</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </div>                <div class="flex justify-end mt-6">
                    <button type="button" data-action="close-modal" data-target="editModal" class="btn-secondary mr-2">Cancel</button>
                    <button type="submit" class="btn-primary">Update Equipment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views\admin\equipment\index.blade.php ENDPATH**/ ?>