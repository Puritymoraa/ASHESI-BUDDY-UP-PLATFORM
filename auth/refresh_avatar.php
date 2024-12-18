<?php
session_start();

function getRandomAvatar() {
    // Array of different avatar styles available from DiceBear API
    $styles = ['adventurer', 'avataaars', 'big-ears', 'bottts', 'croodles', 'fun-emoji'];
    
    // Randomly select one style from the array
    $style = $styles[array_rand($styles)];
    
    // Generate a unique seed for the avatar
    $seed = uniqid();
    
    // Construct the API URL with the selected style and seed
    $avatarUrl = "https://api.dicebear.com/6.x/{$style}/svg?seed=" . $seed;
    
    return $avatarUrl;
}

// Generate new avatar URL
$newAvatarUrl = getRandomAvatar();

// Store in session
$_SESSION['avatar_url'] = $newAvatarUrl;

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'avatar_url' => $newAvatarUrl
]);

