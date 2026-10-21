@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <div class="card shadow">
        <div class="card-header bg-dark text-white d-flex justify-content-between">
            <h4 class="mb-0">Users List</h4>
            <a href="{{ route('users.create') }}" class="btn btn-light btn-sm">+ Add User</a>
        </div>

        <div class="card-body">

            <!-- FILTER FORM -->
            <form method="GET" action="{{ route('users.index') }}" class="row mb-4">

                <div class="col-md-4 mb-2">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search by name or email"
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-4 mb-2">
                    <select name="company_id" class="form-select">
                        <option value="">All Companies</option>
                        @foreach ($company as $comp)
                            <option value="{{ $comp->cid }}"
                                {{ request('company_id') == $comp->cid ? 'selected' : '' }}>
                                {{ $comp->cname }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 d-flex mb-2 gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Reset</a>
                    <button type="button" onclick="downloadUsersPdf()" class="btn btn-danger" title="Download all records in PDF format">
                        <i class="mdi mdi-file-pdf-box me-1"></i> Download PDF
                    </button>
                </div>

            </form>

            <!-- SUCCESS MESSAGE -->
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- USERS TABLE -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Business</th>
                            <th>Company</th>
                            <th>Reference</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($users as $key => $user)
                            <tr>
                                <td>{{ $users->firstItem() + $key }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->business_name ?? '-' }}</td>

                                <!-- Show Company Name (not ID) -->
                                <td>{{ $user->company->cname ?? '-' }}</td>
                                <td>{{ $user->reference ?? '-' }}</td>

                                <td>
                                    <a href="{{ route('users.edit', $user->id) }}"
                                       class="btn btn-sm btn-warning">Edit</a>

                                    <!-- Delete -->
                                    <form action="{{ route('users.destroy', $user->id) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Delete this user?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No users found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="mt-3">
                {{ $users->withQueryString()->links() }}
            </div>

        </div>
    </div>

</div>

<!-- jsPDF and AutoTable for PDF Download (Local Vendor Assets) -->
<script src="{{ asset('assets/vendor/jspdf/jspdf.umd.min.js') }}"></script>
<script src="{{ asset('assets/vendor/jspdf/jspdf.plugin.autotable.min.js') }}"></script>

<script>
function downloadUsersPdf() {
    const usersData = @json($pdfUsers ?? []);

    if (!usersData || usersData.length === 0) {
        alert('No user records found to download.');
        return;
    }

    try {
        if (window.jspdf && window.jspdf.jsPDF) {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'pt', 'a4');

            // Document Title & Info
            doc.setFontSize(16);
            doc.setFont('helvetica', 'bold');
            doc.text('Users List', 40, 40);

            doc.setFontSize(10);
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(100);
            doc.text(`Total Records: ${usersData.length} | Generated: ${new Date().toLocaleDateString()}`, 40, 56);

            // AutoTable with requested columns: #, Name, Email, Business, Company, Reference
            doc.autoTable({
                startY: 70,
                head: [['#', 'Name', 'Email', 'Business', 'Company', 'Reference']],
                body: usersData,
                theme: 'striped',
                headStyles: {
                    fillColor: [33, 37, 41],
                    textColor: [255, 255, 255],
                    fontStyle: 'bold',
                    fontSize: 9
                },
                bodyStyles: {
                    fontSize: 8,
                    textColor: [33, 37, 41]
                },
                alternateRowStyles: {
                    fillColor: [248, 249, 250]
                },
                columnStyles: {
                    0: { cellWidth: 30, halign: 'center' },
                    1: { cellWidth: 100 },
                    2: { cellWidth: 120 },
                    3: { cellWidth: 105 },
                    4: { cellWidth: 80 },
                    5: { cellWidth: 80 }
                },
                margin: { top: 40, right: 40, bottom: 40, left: 40 },
                didDrawPage: function(data) {
                    const pageCount = doc.internal.getNumberOfPages();
                    doc.setFontSize(8);
                    doc.setTextColor(150);
                    doc.text('Page ' + data.pageNumber + ' of ' + pageCount, doc.internal.pageSize.width - 70, doc.internal.pageSize.height - 20);
                }
            });

            doc.save(`Users_List_${usersData.length}_Records.pdf`);
            return;
        }
    } catch (err) {
        console.error('jsPDF error, falling back to print:', err);
    }

    // Fallback printable view using hidden iframe (immune to popup blocker)
    printUsersReport(usersData);
}

function printUsersReport(data) {
    let rowsHtml = '';
    data.forEach(r => {
        rowsHtml += `<tr>
            <td style="text-align:center;">${r[0]}</td>
            <td>${r[1]}</td>
            <td>${r[2]}</td>
            <td>${r[3]}</td>
            <td>${r[4]}</td>
            <td>${r[5] ?? '-'}</td>
        </tr>`;
    });

    const iframe = document.createElement('iframe');
    iframe.style.position = 'fixed';
    iframe.style.right = '0';
    iframe.style.bottom = '0';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = '0';
    document.body.appendChild(iframe);

    const doc = iframe.contentWindow.document;
    doc.open();
    doc.write(`
        <html>
        <head>
            <title>Users List</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; font-size: 12px; }
                h2 { margin-bottom: 5px; color: #212529; }
                p { color: #666; margin-bottom: 15px; font-size: 11px; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
                th { background-color: #212529; color: #fff; font-size: 11px; }
                tr:nth-child(even) { background-color: #f8f9fa; }
            </style>
        </head>
        <body>
            <h2>Users List</h2>
            <p>Total Records: ${data.length} | Generated: ${new Date().toLocaleDateString()}</p>
            <table>
                <thead>
                    <tr>
                        <th style="width: 35px; text-align: center;">#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Business</th>
                        <th>Company</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>${rowsHtml}</tbody>
            </table>
        </body>
        </html>
    `);
    doc.close();

    setTimeout(() => {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
        setTimeout(() => {
            if (document.body.contains(iframe)) {
                document.body.removeChild(iframe);
            }
        }, 2000);
    }, 500);
}
</script>
@endsection