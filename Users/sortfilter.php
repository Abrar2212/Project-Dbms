<div class="filterSort">

<div class="dropdown d-flex">
    <div class="suggestion">
        <h3>Suggestions</h3>
    </div>
    <button class="btn but btn-success dropdown-toggle " type="button" data-bs-toggle="dropdown" aria-expanded="false" style="margin-left: 10px;">
        Sort
    </button>

    <ul class="dropdown-menu dropdown-menu-dark">
    <li><a class="dropdown-item <?= $sort_order == 'A-Z' ? 'active' : '' ?>"
            href="?sort=A-Z<?= !empty($search_location) ? '&location=' . urlencode($search_location) : '' ?>">A-Z</a></li>
    <li><a class="dropdown-item <?= $sort_order == 'Z-A' ? 'active' : '' ?>"
            href="?sort=Z-A<?= !empty($search_location) ? '&location=' . urlencode($search_location) : '' ?>">Z-A</a></li>
    <li><a class="dropdown-item <?= $sort_order == 'Low-High' ? 'active' : '' ?>"
            href="?sort=Low-High<?= !empty($search_location) ? '&location=' . urlencode($search_location) : '' ?>">Low to High</a></li>
    <li><a class="dropdown-item <?= $sort_order == 'High-Low' ? 'active' : '' ?>"
            href="?sort=High-Low<?= !empty($search_location) ? '&location=' . urlencode($search_location) : '' ?>">High to Low</a></li>
    
</ul>

</div>





</div>
