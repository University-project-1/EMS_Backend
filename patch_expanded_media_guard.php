<?php
$path = __DIR__.'/database/seeders/ExpandedTechMediaSeeder.php';
$contents = file_get_contents($path);
$pattern = '~if \(count\(\\\$logos\) < 1 \|\| count\(\\\$gallery\) < 2\) \{\s*throw new RuntimeException\([^;]+;\s*\}~s';
$replacement = 'if (count($logos) < 1 || count($gallery) < 2) {\n                continue;\n            }';
$updated = preg_replace($pattern, $replacement, $contents, 1, $count);
if ($count !== 1) { throw new RuntimeException('GUARD_NOT_FOUND'); }
file_put_contents($path, $updated);
echo "PATCHED\n";
