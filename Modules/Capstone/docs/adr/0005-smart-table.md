# Smart data-table stays canonical

`x-capstone::data-table` (Alpine `search/sort/page/bulk/grouped/loading/error` + `capstoneTable` methods) stays canonical for record lists. Static `x-df-table` and Penguin tables are presentational only and would drop pagination, sorting, and bulk actions.
