Zfc-User Simple FM Authentication Adapter
=========================================

This module provides authentication via SimpleFM to a FileMaker database.


Configuration
=============

Your ```zfcuser.global.php``` must have these values:

```php
'user_entity_class' => 'Soliant\ZfcUserSimpleFM\Entity\User',
'auth_adapters' => [100 => 'Soliant\ZfcUserSimpleFM\Authentication\Adapter\SimpleFM'],
'table_name' => 'user', // The name of your layout
```

