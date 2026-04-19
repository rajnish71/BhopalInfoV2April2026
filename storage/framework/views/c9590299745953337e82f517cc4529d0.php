<?php $__env->startSection('title', ($post->seo_title ?: $post->title) . ' | Bhopal Info'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .bg-brand { background-color: #B71C1C; }
    .max-w-prose { max-width: 65ch; margin: 0 auto; }
    .content-block-label { color: #B71C1C; font-weight: 900; text-transform: uppercase; font-size: 11px; margin-top: 2rem; border-b: 2px solid #EEE; display: block; }
    article { line-height: 1.6; }
</style>

<article class="py-12">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <div class="flex items-center space-x-2 text-[10px] font-black uppercase text-gray-400 mb-4">
                <span><?php echo e($post->category?->name); ?></span>
                <span>/</span>
                <span style="color:#B71C1C;"><?php echo e($post->area?->name ?: 'CITY WIDE'); ?></span>
            </div>
            <h1 class="text-4xl font-black uppercase tracking-tight leading-tight mb-6"><?php echo e($post->title); ?></h1>
            <p class="text-lg font-bold italic text-gray-500 border-l-4 border-gray-200 pl-6"><?php echo e($post->summary); ?></p>
        </div>

        <div class="mb-12 aspect-video bg-gray-100">
            <img src="<?php echo e($post->featured_image && file_exists(public_path('storage/'.$post->featured_image)) ? asset('storage/'.$post->featured_image) : asset('images/hero.jpg')); ?>" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='<?php echo e(asset('images/hero.jpg')); ?>';">
        </div>

        <div class="grid grid-cols-12 gap-12">
            <div class="col-span-8">
                <?php
                    $blocks = is_array($post->content_blocks) ? $post->content_blocks : json_decode($post->content_blocks, true);
                ?>
                <?php if($blocks): ?>
                    <?php $__currentLoopData = $blocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="content-block-label"><?php echo e($label); ?></span>
                        <div class="mt-4 text-gray-800"><?php echo e($text); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="mt-4 text-gray-800">No content available for this post.</div>
                <?php endif; ?>
            </div>
            <div class="col-span-4">
                <div class="bg-gray-50 p-6 border border-gray-100 sticky top-8">
                    <section class="mb-8">
                        <h4 class="text-[10px] font-black uppercase text-gray-400 mb-2">VERIFIED SOURCE</h4>
                        <p class="text-sm font-bold"><?php echo e($post->source?->name ?? 'Bhopal Info'); ?></p>
                    </section>
                    <section class="mb-8">
                        <h4 class="text-[10px] font-black uppercase text-gray-400 mb-2">PUBLISHED ON</h4>
                        <p class="text-sm font-bold"><?php echo e($post->published_at?->format('d M Y, H:i') ?? 'N/A'); ?></p>
                    </section>
                    <section>
                        <h4 class="text-[10px] font-black uppercase text-gray-400 mb-2">URGENCY</h4>
                        <span class="px-2 py-0.5 text-[10px] font-black uppercase <?php echo e($post->urgency_level === 'critical' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-600'); ?>">
                            <?php echo e($post->urgency_level); ?>

                        </span>
                    </section>
                </div>
            </div>
        </div>
    </div>
</article>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(theme_view('layouts.app'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/bhopal-admin-core/current/resources/views/news/show.blade.php ENDPATH**/ ?>