<?php $__env->startSection('title', 'News & Updates | Bhopal Info'); ?>

<?php $__env->startSection('content'); ?>

<div style="padding: 40px 0 20px;">
    <p style="color:#B71C1C; font-weight:900; text-transform:uppercase; font-size:10px; letter-spacing:.08em; margin-bottom:8px;">Civic Infrastructure // Bhopal</p>
    <h1 style="font-size:32px; font-weight:900; text-transform:uppercase; letter-spacing:-.01em; margin:0 0 8px;">News & Updates</h1>
    <p style="font-size:13px; color:#888; margin:0;">Verified civic updates, alerts and service information — filtered by area.</p>
</div>


<form method="GET" action="/news" style="margin-bottom:24px; padding:16px 0; border-top:1px solid #eee; border-bottom:1px solid #eee;">
    <div style="display:flex; flex-wrap:wrap; align-items:center; gap:12px;">
        <span style="color:#B71C1C; font-weight:900; text-transform:uppercase; font-size:10px;">Filter:</span>

        <select name="area" onchange="this.form.submit()" style="border:1px solid #E5E7EB; padding:5px 10px; font-size:11px; font-weight:700; text-transform:uppercase; background:#fff; color:#111; cursor:pointer;">
            <option value="">All Areas</option>
            <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($area->id); ?>" <?php echo e(request('area') == $area->id ? 'selected' : ''); ?>><?php echo e($area->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>

        <select name="category" onchange="this.form.submit()" style="border:1px solid #E5E7EB; padding:5px 10px; font-size:11px; font-weight:700; text-transform:uppercase; background:#fff; color:#111; cursor:pointer;">
            <option value="">All Categories</option>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>

        <select name="urgency" onchange="this.form.submit()" style="border:1px solid #E5E7EB; padding:5px 10px; font-size:11px; font-weight:700; text-transform:uppercase; background:#fff; color:#111; cursor:pointer;">
            <option value="">All Urgency</option>
            <option value="critical" <?php echo e(request('urgency') === 'critical' ? 'selected' : ''); ?>>Critical</option>
            <option value="important" <?php echo e(request('urgency') === 'important' ? 'selected' : ''); ?>>Important</option>
            <option value="normal" <?php echo e(request('urgency') === 'normal' ? 'selected' : ''); ?>>Normal</option>
        </select>

        <?php if(request()->hasAny(['area', 'category', 'urgency'])): ?>
            <a href="/news" style="border:1px solid #E5E7EB; padding:4px 12px; font-size:11px; font-weight:700; text-transform:uppercase; background:#fff; color:#111; text-decoration:none;">Clear</a>
        <?php endif; ?>
    </div>
</form>


<div style="display:flex; justify-content:space-between; margin-bottom:20px;">
    <p style="font-size:10px; font-weight:900; text-transform:uppercase; color:#9CA3AF;"><?php echo e($posts->total()); ?> updates</p>
    <p style="font-size:10px; font-weight:900; text-transform:uppercase; color:#9CA3AF;">Page <?php echo e($posts->currentPage()); ?> of <?php echo e($posts->lastPage()); ?></p>
</div>


<?php if($posts->count()): ?>
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:40px;">
        <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="border:1px solid #E5E7EB; padding:20px; transition:border-color .15s;" onmouseover="this.style.borderColor='#B71C1C'" onmouseout="this.style.borderColor='#E5E7EB'">

                <div style="font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:.08em; color:#9CA3AF; display:flex; gap:6px; flex-wrap:wrap;">
                    <span style="color:#B71C1C;"><?php echo e($post->area?->name ?? 'City Wide'); ?></span>
                    <span>/</span>
                    <span><?php echo e($post->category?->name ?? 'General'); ?></span>
                    <span>/</span>
                    <span><?php echo e($post->published_at?->diffForHumans() ?? '—'); ?></span>
                </div>

                <h2 style="font-size:15px; font-weight:900; text-transform:uppercase; line-height:1.25; margin:8px 0 6px;">
                    <a href="/news/<?php echo e($post->slug ?? $post->id); ?>" style="text-decoration:none; color:inherit;"><?php echo e($post->title); ?></a>
                </h2>

                <?php if($post->summary): ?>
                    <p style="font-size:13px; color:#555; line-height:1.5; margin:0 0 12px;"><?php echo e(\Illuminate\Support\Str::limit($post->summary, 110)); ?></p>
                <?php endif; ?>

                <div style="display:flex; align-items:center; justify-content:space-between; margin-top:12px;">
                    <?php
                        $urgencyStyle = match($post->urgency_level) {
                            'critical' => 'background:#B71C1C; color:#fff;',
                            'important' => 'background:#111; color:#fff;',
                            default => 'background:#F3F4F6; color:#555;'
                        };
                    ?>
                    <span style="<?php echo e($urgencyStyle); ?> display:inline-block; padding:2px 8px; font-size:9px; font-weight:900; text-transform:uppercase;"><?php echo e($post->urgency_level); ?></span>
                    <a href="/news/<?php echo e($post->slug ?? $post->id); ?>" style="font-size:10px; font-weight:900; text-transform:uppercase; color:#B71C1C; text-decoration:none; letter-spacing:.08em;">Read &rarr;</a>
                </div>

            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if($posts->hasPages()): ?>
        <div style="display:flex; justify-content:center; gap:4px; flex-wrap:wrap; margin-bottom:40px;">
            <a href="<?php echo e($posts->previousPageUrl() ?? '#'); ?>" style="display:inline-block; padding:6px 14px; border:1px solid #E5E7EB; font-size:11px; font-weight:700; text-transform:uppercase; text-decoration:none; color:#111; <?php echo e($posts->onFirstPage() ? 'opacity:.35; pointer-events:none;' : ''); ?>">&larr; Prev</a>
            <?php $__currentLoopData = $posts->getUrlRange(1, $posts->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($url); ?>" style="display:inline-block; padding:6px 14px; border:1px solid #E5E7EB; font-size:11px; font-weight:700; text-transform:uppercase; text-decoration:none; <?php echo e($page === $posts->currentPage() ? 'background:#B71C1C; color:#fff; border-color:#B71C1C;' : 'color:#111;'); ?>"><?php echo e($page); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

<?php else: ?>
    <div style="padding:60px 20px; text-align:center; border:1px dashed #E5E7EB; margin-bottom:40px;">
        <p style="color:#B71C1C; font-weight:900; text-transform:uppercase; font-size:10px; margin-bottom:8px;">No Results</p>
        <p style="font-size:13px; color:#888;">No published updates match your filters.</p>
        <?php if(request()->hasAny(['area', 'category', 'urgency'])): ?>
            <a href="/news" style="display:inline-block; margin-top:16px; border:1px solid #E5E7EB; padding:4px 12px; font-size:11px; font-weight:700; text-transform:uppercase; text-decoration:none; color:#111;">Clear Filters</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make(theme_view('layouts.app'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/bhopal-admin-core/current/resources/views/news/index.blade.php ENDPATH**/ ?>