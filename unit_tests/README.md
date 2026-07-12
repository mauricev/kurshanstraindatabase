# Search regression tests

Run from the `www-current` directory:

```sh
php unit_tests/search_regression_tests.php
```

The runner uses the app's existing `Peri_Database` connection and reads fixture IDs from the local database when available. It does not write to the database or change schema.

The tests cover:

- comments combined with gene/allele/transgene strain searches
- plasmid antibiotic and fluorotag searches
- OR-vs-AND query-clause shape for balancers, antibiotics, and fluorotags
- PHP warnings/notices in exercised search paths, which are treated as failures

