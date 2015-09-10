# Data Grid Change Log

This project follows [Semantic Versioning](CONTRIBUTING.md).

## Proposals

We do not give estimated times for completion on `Accepted` Proposals.

- [Accepted](https://github.com/cartalyst/data-grid/labels/Accepted)
- [Rejected](https://github.com/cartalyst/data-grid/labels/Rejected)

---

### v3.0.4 - 2015-07-05

`FIXED`

- A bug introduced by group workaround.

### v3.0.3 - 2015-07-02

`FIXED`

- Pagination Error with Grouped Queries.
- Some issues introduced by Laravel 5.1.

`UPDATED`

- `totalCount` and tweak grouped queries count.

### v3.0.2 - 2015-02-24

`FIXED`

- Incorrect filtering when using multiple global filters on the collection handler.

### v3.0.1 - 2015-02-20

`REVISED`

- Allow resetting group filters.

### v3.0.0 - 2015-02-17

- Updated for Laravel 5.

### v2.0.7 - 2015-07-05

`FIXED`

- A bug introduced by group workaround.

### v2.0.6 - 2015-02-24

`FIXED`

- Incorrect filtering when using multiple global filters on the collection handler.

### v2.0.5 - 2015-02-20

`REVISED`

- Allow resetting group filters.

### v2.0.4 - 2015-01-29

`FIXED`

- A bug preventing threshold/throttle from correctly being added to the hash.

### v2.0.3 - 2014-11-06

`ADDED`

- A setter `setDataHandlerMappings` to the environment to allow overriding the entire mappings array during runtime.

### v2.0.2 - 2014-10-13

`FIXED`

- An issue on the Data Grid Javascript plugin where the `data-reset` event was being wrongly propagated when the anchor tag was `href="#"`.

### v2.0.1 - 2014-09-29

`FIXED`

- A bug that prevented overriding throttle and threshold through the PHP settings array.

### v2.0.0 - 2014-08-18

- Filter Eloquent models, queries or relationships using the `DatabaseHandler`.
- Filter Illuminate Collections using the `CollectionHandler`.
- Create your own handlers by implementing the `HandlerInterface`.
- Download results. (csv, json, pdf)
- Underscore templating.
- Javascript plugin.
