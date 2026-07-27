<div class="sv-list" data-students-count="{{ $students->count() }}">
    @if ($students->count() > 0)
        <div class="sv-list__meta">
            <span>{{ get_phrase('العدد الإجمالي') }}</span>
            <strong>{{ $students->count() }}</strong>
        </div>
        <div class="sv-list__table-wrap">
            <table class="sv-list__table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ get_phrase('الاسم') }}</th>
                        <th>{{ get_phrase('البريد الإلكتروني') }}</th>
                        <th>{{ get_phrase('الهاتف') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $index => $student)
                        <tr>
                            <td>
                                <span class="sv-list__index">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <div class="sv-list__user">
                                    <span class="sv-list__avatar">{{ mb_substr($student->name, 0, 1) }}</span>
                                    <span>{{ $student->name }}</span>
                                </div>
                            </td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->phone ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="sv-empty sv-empty--panel">
            <i class="fi-rr-user-slash"></i>
            <p>{{ get_phrase('لا توجد بيانات') }}</p>
        </div>
    @endif
</div>
