<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Verification</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 border-t-4 border-[#B71C1C] shadow-lg max-w-sm w-full">
        <h2 class="text-2xl font-bold mb-4 text-center">2FA Verification</h2>
        <p class="text-sm text-gray-600 mb-6 text-center">A verification code has been generated. Please enter it below.</p>
        <form action="{{ route('admin.2fa.verify') }}" method="POST">
            @csrf
            <div class="mb-4">
                <input type="text" name="code" placeholder="Enter 6-digit code" required class="w-full border-gray-300 focus:border-[#B71C1C] focus:ring-[#B71C1C] text-center text-2xl tracking-widest font-bold">
                @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="w-full bg-[#B71C1C] text-white py-3 font-bold hover:bg-red-800 transition">Verify</button>
        </form>
    </div>
</body>
</html>