<div style="display:flex; gap:30px;">

    
    <div style="flex:2;">
        <h2 style="margin-bottom:12px;">Latest Updates</h2>

        <?php if($news && count($news)): ?>
            <?php $__currentLoopData = $news; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="border:1px solid #eee; padding:12px; margin-bottom:12px; border-radius:8px;">
                
                <div style="font-size:11px; color:#888;">
                    <?php echo e($item->category->name ?? 'General'); ?> • <?php echo e(optional($item->published_at)->diffForHumans()); ?>

                </div>

                <div style="font-weight:600;">
                    <a href="/news/<?php echo e($item->slug ?? $item->id); ?>" style="text-decoration:none; color:inherit;">
                        <?php echo e($item->title); ?>

                    </a>
                </div>

                <div style="font-size:12px; color:#666;">
                    <?php echo e(\Illuminate\Support\Str::limit($item->excerpt ?? $item->summary, 100)); ?>

                </div>

            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <div style="color:#888;">No recent news available.</div>
        <?php endif; ?>
    </div>

    
    <div style="flex:1;">
        <h2 style="margin-bottom:12px;">Events in Bhopal</h2>

        <?php if($upcomingEvents && count($upcomingEvents)): ?>
            <?php $__currentLoopData = $upcomingEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="border:1px solid #eee; padding:12px; margin-bottom:12px; border-radius:8px;">

                <div style="font-size:11px;">
                    📅 <?php echo e(\Carbon\Carbon::parse($event->start_datetime)->format('d M, h:i A')); ?>

                </div>

                <div style="font-weight:600;">
                    <a href="/events/<?php echo e($event->slug ?? $event->id); ?>" style="text-decoration:none; color:inherit;">
                        <?php echo e($event->title); ?>

                    </a>
                </div>

                <div style="font-size:12px; color:#666;">
                    📍 <?php echo e($event->location_name ?? $event->venue ?? 'Bhopal'); ?>

                </div>

            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <div style="color:#888;">No upcoming events at this time.</div>
        <?php endif; ?>

    </div>
</div>
<?php /**PATH /var/www/bhopal-admin-core/current/resources/views/themes/modern2026/sections/latest-updates.blade.php ENDPATH**/ ?>