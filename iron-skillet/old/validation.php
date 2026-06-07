<?php

require_once 'IronSkilletManager.php';

// Define the path where your iron-skillet repository files are cloned locally
$ironSkilletPath = __DIR__ . '/pan-os-php/iron-skillet';
$ironSkilletPath = __DIR__ ;

try {
    // 1. Initialize the manager class
    $manager = new IronSkilletManager($ironSkilletPath);

    // 2. Scan the repository for PAN-OS versions and file mappings
    echo "Scanning Iron-Skillet Repository...\n";
    $repoStructure = $manager->scanRepository();

    // 3. Define your target Array of URL categories to check/compare against
    $myUrlCategories = [
        'gambling',
        'hacking',
        'phishing',
        'cryptocurrency',
        'new-custom-category-xyz' // This one will likely be missing
    ];

    // 4. Process a specific version and profile type
    $targetVersion = 'v10.0'; // Change this to any version found in the repository folder structure

    echo "Auditing URL Categories for {$targetVersion} (alert profiles)...\n";
    $result = $manager->auditAndSyncUrlCategories($myUrlCategories, $targetVersion, 'alert');

    // 5. Output Results
    echo "----------------------------------------\n";
    echo "PAN-OS Version: " . $result['version'] . "\n";
    echo "Profile Type: " . $result['type'] . "\n";
    echo "Found Existing Categories: " . implode(', ', $result['existing_categories']) . "\n";

    if (!empty($result['missing_categories_added'])) {
        echo "WARNING: Missing categories detected and added: " . implode(', ', $result['missing_categories_added']) . "\n";
    } else {
        echo "SUCCESS: All categories perfectly matched!\n";
    }
    echo "----------------------------------------\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}