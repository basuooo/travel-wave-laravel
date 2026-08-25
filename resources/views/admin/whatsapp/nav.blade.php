<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-2 bg-light rounded">
        <ul class="nav nav-pills gap-1 flex-wrap">
            <li class="nav-item">
                <a class="nav-link fw-bold text-nowrap {{ request()->routeIs('admin.whatsapp.dashboard') ? 'active' : '' }}" href="{{ route('admin.whatsapp.dashboard') }}">
                    📊 Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-bold text-nowrap {{ request()->routeIs('admin.whatsapp.accounts.*') ? 'active' : '' }}" href="{{ route('admin.whatsapp.accounts.index') }}">
                    📱 WhatsApp Accounts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-bold text-nowrap {{ request()->routeIs('admin.whatsapp.conversations.*') ? 'active' : '' }}" href="{{ route('admin.whatsapp.conversations.index') }}">
                    💬 Conversations / Inbox
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-bold text-nowrap {{ request()->routeIs('admin.whatsapp.contacts.*') ? 'active' : '' }}" href="{{ route('admin.whatsapp.contacts.index') }}">
                    👥 WhatsApp Contacts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-bold text-nowrap btn-danger text-white {{ request()->routeIs('admin.whatsapp.retargeting.*') ? 'active' : '' }}" href="{{ route('admin.whatsapp.retargeting.create') }}">
                    🎯 Retargeting
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-bold text-nowrap {{ request()->routeIs('admin.whatsapp.bulk.*') ? 'active' : '' }}" href="{{ route('admin.whatsapp.bulk.index') }}">
                    🚀 Bulk Campaigns
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-bold text-nowrap {{ request()->routeIs('admin.whatsapp.templates.*') ? 'active' : '' }}" href="{{ route('admin.whatsapp.templates.index') }}">
                    📑 Templates
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-bold text-nowrap {{ request()->routeIs('admin.whatsapp.sequences.*') ? 'active' : '' }}" href="{{ route('admin.whatsapp.sequences.index') }}">
                    🔄 Sequences
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-bold text-nowrap {{ request()->routeIs('admin.whatsapp.reports.*') ? 'active' : '' }}" href="{{ route('admin.whatsapp.reports.index') }}">
                    📈 Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-bold text-nowrap {{ request()->routeIs('admin.whatsapp.logs.*') ? 'active' : '' }}" href="{{ route('admin.whatsapp.logs.index') }}">
                    📋 Logs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-bold text-nowrap {{ request()->routeIs('admin.whatsapp.settings.*') ? 'active' : '' }}" href="{{ route('admin.whatsapp.settings.index') }}">
                    ⚙️ Settings & Blacklist
                </a>
            </li>
        </ul>
    </div>
</div>
