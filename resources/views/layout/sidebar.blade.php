<aside class="app-sidebar">
        <div class="sidebar-content p-3">
            <ul class="sidebar-menu">
            <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
            </li>
            <li class="{{ request()->routeIs('categories.index') ? 'active' : '' }}">
    <a href="{{ route('categories.index') }}"><i class="bi bi-people me-2"></i>Category</a>
</li>

<li class="{{ request()->routeIs('brands.index') ? 'active' : '' }}"> {{-- Changed route name here --}}
    <a href="{{ route('brands.index') }}"><i class="bi bi-people me-2"></i>brands</a> {{-- Changed route name here --}}
</li>
                <li>
                    <a href="#"><i class="bi bi-envelope me-2"></i>Messages <span class="badge bg-danger float-end">5</span></a>
                </li>
                <li>
                    <a href="#"><i class="bi bi-calendar-check me-2"></i>Calendar</a>
                </li>
                <li>
                    <a href="#"><i class="bi bi-file-earmark-text me-2"></i>Reports</a>
                </li>
                <li>
                    <a href="#"><i class="bi bi-gear me-2"></i>Settings</a>
                </li>
            </ul>
        </div>
    </aside>    