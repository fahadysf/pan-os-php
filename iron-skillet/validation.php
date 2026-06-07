<?php
require_once 'IronSkilletManager.php';

// Paths configuration
$ironSkilletPath = __DIR__ . '/pan-os-php/iron-skillet';
$ironSkilletPath = __DIR__ ;

// You can use the local path if downloaded, or the GitHub raw URL directly
#$predefinedXmlSource = 'https://raw.githubusercontent.com/swaschkut/pan-os-php/main/lib/object-classes/predefined.xml';
$predefinedXmlSource = __DIR__ . '/../lib/object-classes/predefined.xml';

try {
    // 1. Instantiate class with paths
    $manager = new IronSkilletManager($ironSkilletPath, $predefinedXmlSource);

    // 2. Read the source URL categories dynamically from predefined.xml
    echo "Parsing categories from predefined.xml...\n";
    $urlCategories = $manager->getPredefinedUrlCategories();
    echo "Found " . count($urlCategories) . " master URL categories.\n\n";

    // 3. Scan Iron-Skillet repository structure
    echo "Scanning Iron-Skillet folders...\n";
    $manager->scanRepository();

    // 4. Audit a targeted version (e.g., 'panos_v10.0' or 'v10.1' depending on directory naming)
    $targetVersion = 'panos_v10.0';

    echo "Auditing differences for version: {$targetVersion}...\n";
    $result = $manager->auditAndSyncUrlCategories($urlCategories, $targetVersion, 'alert');

    // 5. Output audit metrics
    echo "----------------------------------------\n";
    echo "Audit Complete for: " . $result['version'] . " (" . $result['type'] . ")\n";
    echo "Categories Checked: " . $result['total_predefined_checked'] . "\n";
    echo "Categories Already Present: " . count($result['existing_categories']) . "\n";
    echo "Categories Missing (and appended): " . count($result['missing_categories_added']) . "\n";

    if (!empty($result['missing_categories_added'])) {
        echo "List of Added Entries: " . implode(', ', $result['missing_categories_added']) . "\n";
    }
    echo "----------------------------------------\n";

} catch (Exception $e) {
    echo "Error execution stopped: " . $e->getMessage() . "\n";
}