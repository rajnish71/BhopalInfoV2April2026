<?php $__env->startSection('title', 'Events in Bhopal | Bhopal Info'); ?>

<?php $__env->startSection('content'); ?>

<div style="padding: 40px 0 20px;">
    <p style="color:#B71C1C; font-weight:900; text-transform:uppercase; font-size:10px; letter-spacing:.08em; margin-bottom:8px;">Civic Infrastructure // Bhopal</p>
    <h1 style="font-size:32px; font-weight:900; text-transform:uppercase; letter-spacing:-.01em; margin:0 0 8px;">Upcoming Events</h1>
    <p style="font-size:13px; color:#888; margin:0;">Cultural, civic, and community events happening across the city.</p>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:20px; margin-bottom:40px; padding-top:20px; border-top:1px solid #eee;">

<?php $__empty_1 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div style="background: white; border: 1px solid #E5E7EB; padding: 20px; transition: border-color .15s;" onmouseover="this.style.borderColor='#B71C1C'" onmouseout="this.style.borderColor='#E5E7EB'">
        <div style="font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:.08em; color:#9CA3AF; margin-bottom:10px;">
            <span style="color:#B71C1C;">📍 <?php echo e($event->venue); ?></span>
        </div>
        
        <h2 style="font-size:18px; font-weight:900; text-transform:uppercase; line-height:1.2; margin:0 0 10px;">
            <a href="/events/<?php echo e($event->slug); ?>" style="text-decoration:none; color:inherit;"><?php echo e($event->title); ?></a>
        </h2>

        <div style="font-size:12px; color:#555; margin-bottom:15px;">
            📅 <?php echo e(\Carbon\Carbon::parse($event->start_datetime)->format('D, d M Y - H:i')); ?>

        </div>

        <p style="font-size:13px; color:#666; line-height:1.5; margin:0 0 20px;">
            <?php echo e(\Illuminate\Support\Str::limit($event->description, 120)); ?>

        </p>

        <a href="/events/<?php echo e($event->slug); ?>" style="font-size:10px; font-weight:900; text-transform:uppercase; color:#B71C1C; text-decoration:none; letter-spacing:.08em;">Event Details &rarr;</a>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div style="grid-column: 1 / -1; padding:60px 20px; text-align:center; border:1px dashed #E5E7EB;">
        <p style="font-size:13px; color:#888;">No upcoming events found at the moment.</p>
    </div>
<?php endif; ?>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make(theme_view('layouts.app'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/bhopal-admin-core/current/resources/views/events/index.blade.php ENDPATH**/ ?>