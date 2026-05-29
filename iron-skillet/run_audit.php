<?php
require_once 'IronSkilletManager.php';

// Path Setup
// Paths configuration
$ironSkilletPath = __DIR__ . '/pan-os-php/iron-skillet';
$ironSkilletPath = __DIR__ ;

// You can use the local path if downloaded, or the GitHub raw URL directly
#$predefinedXmlSource = 'https://raw.githubusercontent.com/swaschkut/pan-os-php/main/lib/object-classes/predefined.xml';
$predefinedXmlSource = __DIR__ . '/../lib/object-classes/predefined.xml';


try {
    $manager = new IronSkilletManager($ironSkilletPath, $predefinedXmlSource);

    echo "Fetching predefined URL categories...\n";
    $masterCategories = $manager->getPredefinedUrlCategories();
    echo "Loaded " . count($masterCategories) . " master URL categories.\n\n";

    echo "Scanning Iron-Skillet repositories...\n";
    $repoStructure = $manager->scanRepository();

    echo "=================================================================\n";
    echo "ALPHABETICALLY SORTED SYNCHRONIZATION RUNNING\n";
    echo "=================================================================\n";

    foreach ($repoStructure as $version => $buckets) {
        echo "-----------------------------------------------------------------\n";
        echo " 📂 PAN-OS PATH: $version\n";
        echo "-----------------------------------------------------------------\n";

        if (empty($buckets['url_filtering'])) {
            echo "   (No profiles_url_filtering.xml tracking item detected)\n\n";
            continue;
        }

        foreach ($buckets['url_filtering'] as $fileInfo) {
            echo "  📄 Target File: {$fileInfo['name']}\n";

            $syncResults = $manager->syncMissingToAlertSection($fileInfo['path'], $masterCategories);

            if (empty($syncResults)) {
                echo "     ℹ️  No targets to change found in this document.\n\n";
                continue;
            }

            foreach ($syncResults as $profileName => $metrics) {
                echo "\n     🛡️ Profile: \"{$profileName}\"\n";

                echo "          ├── 🔄 <alert> Section Status:\n";
                if (!empty($metrics['added_to_alert'])) {
                    $addedCount = count($metrics['added_to_alert']);
                    echo "          │     └── Added {$addedCount} missing categories.\n";
                }
                echo "          │     └── ✅ Status: All members are now sorted alphabetically (A-Z).\n";

                if (empty($metrics['still_missing_in_cred_enforcement'])) {
                    echo "          └── ✅ <credential-enforcement> Section: Perfect match.\n";
                } else {
                    $missingCredCount = count($metrics['still_missing_in_cred_enforcement']);
                    echo "          └── ⚠️  Note: <credential-enforcement> remains missing ($missingCredCount) categories.\n";
                }
            }
            echo "\n";
        }
    }

    echo "Done! The target profiles inside profiles_url_filtering.xml files are now completely synced and perfectly sorted alphabetically.\n";

} catch (Exception $e) {
    echo "\nTerminated on Error: " . $e->getMessage() . "\n";
}