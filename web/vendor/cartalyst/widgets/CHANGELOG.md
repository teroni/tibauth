# Widgets Change Log

This project follows [Semantic Versioning](CONTRIBUTING.md).

## Proposals

We do not give estimated times for completion on `Accepted` Proposals.

- [Accepted](https://github.com/cartalyst/widgets/labels/Accepted)
- [Rejected](https://github.com/cartalyst/widgets/labels/Rejected)

---

### v1.1.3 - 2015-07-06

`FIXED`

- Regular expression to match the Laravel `createMatcher` method output.

### v1.1.2 - 2015-07-02

`REVISED`

- Blade registration extension so it works with Laravel 5.1.

### v1.1.1 - 2015-02-17

`REVISED`

- Loosened dependencies to work with Laravel 5.

### v1.1.0 - 2014-12-17

`REMOVED`

- Removed the `try/catch` block from the Blade widget call.

`CHANGED`

- Laravel Service Provider from `Cartalyst\Widgets\WidgetsServiceProvider` to `Cartalyst\Widgets\Laravel\WidgetsServiceProvider`.
- Laravel Facade `Cartalyst\Widgets\Facades\Widget` to `Cartalyst\Widgets\Laravel\Facades\Widget`.

### v1.0.2 - 2014-07-13

- Adding a `try/catch` block to the Blade widget call.

### v1.0.1 - 2013-11-27

- Loosen version constraints.

### v1.0.0 - 2013-05-29

- First Release.
