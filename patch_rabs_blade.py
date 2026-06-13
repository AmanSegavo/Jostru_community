import re

file_path = "d:/Jostru Community Sistem/Jostru_community/resources/views/admin/rabs.blade.php"

with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

target_status = """<td class="text-center">
                                    @if($rab->status == 'PENDING')
                                        <span class="badge badge-soft-warning">⏳ Pending</span>
                                    @elseif($rab->status == 'APPROVED')
                                        <span class="badge badge-soft-success">✅ Approved</span>
                                    @else
                                        <span class="badge badge-soft-danger">❌ Rejected</span>
                                    @endif
                                </td>"""
replace_status = """<td class="text-center">
                                    @if($rab->status == 'PENDING')
                                        <span class="badge badge-soft-warning">⏳ Pending</span>
                                    @elseif($rab->status == 'APPROVED')
                                        <span class="badge badge-soft-success" style="margin-bottom:4px; display:inline-block;">✅ Approved</span>
                                        @php
                                            $terserap = $rab->finances->sum('amount');
                                            $persen = $rab->total_amount > 0 ? min(100, round(($terserap / $rab->total_amount) * 100)) : 0;
                                        @endphp
                                        <div style="font-size:10px; color:var(--text-secondary); text-align:left; margin-top:2px;">Terserap: Rp {{ number_format($terserap, 0, ',', '.') }}</div>
                                        <div class="progress" style="height:6px; background:rgba(0,0,0,0.05); border-radius:10px; margin-top:2px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $persen }}%; border-radius:10px;" aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    @else
                                        <span class="badge badge-soft-danger">❌ Rejected</span>
                                    @endif
                                </td>"""
if "$terserap = $rab->finances->sum('amount');" not in content:
    content = content.replace(target_status, replace_status)
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print("rabs.blade.php patched.")
else:
    print("Already patched rabs.blade.php.")
