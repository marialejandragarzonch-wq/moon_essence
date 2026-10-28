<?php
// Genera e imprime los hashes exactos creados por tu versión de PHP
echo "<b>Admin:</b> " . password_hash('MoonAdmin2026*', PASSWORD_DEFAULT) . "<br>";
echo "<b>Vendedor:</b> " . password_hash('VentasMoon2026!', PASSWORD_DEFAULT) . "<br>";
echo "<b>Cliente:</b> " . password_hash('TiendaEssence55$', PASSWORD_DEFAULT) . "<br>";
?>