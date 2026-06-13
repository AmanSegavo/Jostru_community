import os
import re

files_map = {
    'finances.blade.php': 'admin.finances.export',
    'logs.blade.php': 'admin.logs.export',
    'messages.blade.php': 'admin.messages.export',
    'productions.blade.php': 'admin.productions.export',
    'rabs.blade.php': 'admin.rabs.export',
    'waste_categories.blade.php': 'admin.waste_categories.export',
    'waste_deposits.blade.php': 'admin.waste_deposits.export'
}

base_dir = r'd:\Jostru Community Sistem\Jostru_community\resources\views\admin'
divisions_dir = r'd:\Jostru Community Sistem\Jostru_community\resources\views\admin\divisions'
files_map_div = {'index.blade.php': 'admin.divisions.export'}

def process(filepath, export_route):
    if not os.path.exists(filepath): return
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    btn = f'''<a href="{{{{ route('{export_route}') }}}}" class="btn hover-lift" style="background:rgba(34,197,94,0.1); color:var(--primary-accent); border:1px solid rgba(34,197,94,0.3); padding:0.5rem 1rem; border-radius:12px; font-size:14px; text-decoration:none; display:inline-flex; align-items:center; gap:5px; font-weight:600;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
    Export CSV
</a>'''

    if 'Export CSV' not in content:
        match_flex = re.search(r'(<div class="d-flex[^>]*gap-2[^>]*>)\s*', content)
        if match_flex:
            content = content[:match_flex.end(1)] + '\n' + btn + '\n' + content[match_flex.end(1):]
        else:
            match_h2 = re.search(r'(<h2[^>]*>.*?</h2>)', content)
            if match_h2:
                content = content[:match_h2.end(1)] + f'\n<div class="mt-2 mb-3">{btn}</div>\n' + content[match_h2.end(1):]

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

for f, route in files_map.items():
    process(os.path.join(base_dir, f), route)

for f, route in files_map_div.items():
    process(os.path.join(divisions_dir, f), route)

print("Export buttons added")
