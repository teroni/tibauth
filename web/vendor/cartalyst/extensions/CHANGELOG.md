# Extensions Change Log

This project follows [Semantic Versioning](CONTRIBUTING.md).

## Proposals

We do not give estimated times for completion on `Accepted` Proposals.

- [Accepted](https://github.com/cartalyst/extensions/labels/Accepted)
- [Rejected](https://github.com/cartalyst/extensions/labels/Rejected)

---

### v2.0.0 - 2015-02-17

- Updated for Laravel 5.

### v1.2.0 - 2015-01-28

`Added`

- Caching support.

`Revised`

- The `setConnectionResolver` to accept instances of Laravel's `Illuminate\Database\Capsule\Manager` to integrations outside of Laravel.

### v1.1.4 - 2014-09-12

`Added`

- An IoC alias.

### v1.1.3 - 2014-08-18

`Fixed`

- An issue preventing service providers from booting.

### v1.1.2 - 2014-07-21

`Revised`

- Consistency updates.

`Added`

- enabling/disabling seeders support.

### v1.1.1 - 2014-07-18

`Revised`

- Consistency updates.
- Improved seeders handling.

### v1.1.0 - 2014-07-17

`Revised`

- Switch to PSR4.

`Added`

- Service providers support.

### v1.0.5 - 2014-07-17

`Revised`

- Docblock and consistency updates.

### v1.0.4 - 2014-07-13

`Added`

- Widgets support.
- Seeds support.

`Revised`

- Loosen `cartalyst/dependencies` version constraint to allow all 1.x versions.

### v1.0.3 - 2013-11-27

`Added`

- `getVendor` method.

`Fixed`

- A bug that prevented versions from updating on the database.

`Revised`

- Updated default migrations path to `migrations`.
- Loosen requirements for Laravel 4.1

### v1.0.2 - 2013-06-28

`Fixed`

- Backward compatibility break.

### v1.0.1 - 2013-06-27

`Revised`

- Utilize the new `cartalyst/dependencies` sorter.

### v1.0.0 - 2013-05-29

`Added`

- Create extension instances based on `extension.php` files.
- Manage extensions using the `ExtensionBag`.
