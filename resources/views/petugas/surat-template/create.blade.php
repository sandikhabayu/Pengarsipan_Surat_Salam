@extends('layouts.petugas')

@section('title', 'Buat Surat Baru')

@section('content')
    <style>
        .table-toolbar {
            background: #f5f5f5;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .table-toolbar button {
            padding: 5px 10px;
            border: 1px solid #ccc;
            background: white;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }

        .table-toolbar button:hover {
            background: #e5e5e5;
        }

        .table-toolbar select {
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            background: white;
        }

        .table-controls {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }

        .table-controls label {
            font-size: 12px;
            font-weight: bold;
        }

        .table-controls input[type="number"] {
            width: 60px;
            padding: 3px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        /* Tambahkan di bagian style */
        .table-border-preview {
            position: absolute;
            pointer-events: none;
            border: 2px dashed #007bff;
            z-index: 1000;
        }

        .cell-border-highlight {
            outline: 2px solid #28a745 !important;
            background-color: rgba(40, 167, 69, 0.1) !important;
        }

        .border-controls button.active {
            background-color: #007bff !important;
            color: white !important;
        }

        /* Styling khusus untuk editing tabel di CKEditor */
        .cke_editable table {
            border-collapse: collapse;
            margin: 10px 0;
        }

        .cke_editable table td,
        .cke_editable table th {
            padding: 8px;
            border: 1px solid #ccc;
            min-width: 50px;
            min-height: 30px;
            position: relative;
        }

        .cke_editable table td:hover,
        .cke_editable table th:hover {
            background-color: #f0f8ff;
            outline: 2px solid #4a90e2;
        }

        .cke_editable table td.selected,
        .cke_editable table th.selected {
            background-color: #e6f7ff;
            outline: 2px solid #1890ff;
        }

        /* Styling untuk cell yang sedang aktif */
        .cke_editable table td:focus,
        .cke_editable table th:focus {
            outline: 3px solid #1890ff;
            background-color: #e6f7ff;
        }

        /* Styling untuk tabel tanpa border */
        .cke_editable table.no-border,
        .cke_editable table.no-border td,
        .cke_editable table.no-border th {
            border: none;
        }

        /* Resize handle untuk tabel */
        .cke_editable table .table-resize-handle {
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 10px;
            height: 10px;
            background: #1890ff;
            cursor: nwse-resize;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .cke_editable table:hover .table-resize-handle {
            opacity: 1;
        }

        /* Custom resize handle styling */
        .custom-resize-handle {
            position: absolute;
            width: 20px;
            height: 20px;
            background: #1890ff;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: nwse-resize;
            z-index: 1000;
            user-select: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }

        .custom-resize-handle:hover {
            transform: scale(1.1);
            background: #40a9ff;
        }

        /* Table resize visual feedback */
        .cke_editable table.resizing {
            opacity: 0.8;
            outline: 2px dashed #1890ff;
        }

        /* Column resize indicator */
        .cke_editable table td.resize-column,
        .cke_editable table th.resize-column {
            border-right: 2px solid #1890ff !important;
            position: relative;
        }

        .cke_editable table td.resize-column::after,
        .cke_editable table th.resize-column::after {
            content: '';
            position: absolute;
            right: -3px;
            top: 0;
            height: 100%;
            width: 6px;
            cursor: col-resize;
            background: transparent;
        }

        /* Row resize indicator */
        .cke_editable table tr.resize-row td,
        .cke_editable table tr.resize-row th {
            border-bottom: 2px solid #1890ff !important;
            position: relative;
        }

        .cke_editable table tr.resize-row td::after,
        .cke_editable table tr.resize-row th::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 100%;
            height: 6px;
            cursor: row-resize;
            background: transparent;
        }

        /* Style untuk indentasi seperti Word */
        .tab-indent {
            margin-left: 40px !important;
        }

        .double-tab-indent {
            margin-left: 80px !important;
        }

        .triple-tab-indent {
            margin-left: 120px !important;
        }

        /* Style untuk tab character visual */
        .tab-char {
            display: inline-block;
            width: 40px;
            height: 1px;
            background: transparent;
            position: relative;
        }

        .tab-char::after {
            content: '→';
            position: absolute;
            left: 5px;
            top: -2px;
            font-size: 10px;
            color: #999;
            opacity: 0.5;
        }

        /* Visual feedback untuk tab di editor */
        .cke_editable p.tabbed,
        .cke_editable div.tabbed {
            position: relative;
        }

        .cke_editable p.tabbed::before,
        .cke_editable div.tabbed::before {
            content: '';
            position: absolute;
            left: -20px;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(to right, transparent, #e3f2fd);
            border-left: 1px solid #bbdefb;
        }

        /* Tambahkan di bagian style CSS */
        .cke_editable table[border="0"],
        .cke_editable table[border="none"] {
            border: none !important;
            outline: 1px dashed #ccc !important;
            /* Visual feedback untuk tabel tanpa border */
        }

        .cke_editable table[border="0"] td,
        .cke_editable table[border="0"] th,
        .cke_editable table[border="none"] td,
        .cke_editable table[border="none"] th {
            border: none !important;
        }

        /* Highlight tabel tanpa border */
        .cke_editable table.no-border-indicator {
            position: relative;
        }

        .cke_editable table.no-border-indicator::before {
            content: "No Border";
            position: absolute;
            top: -20px;
            left: 0;
            background: #dc3545;
            color: white;
            padding: 2px 5px;
            font-size: 10px;
            border-radius: 3px;
            opacity: 0.7;
        }

        .mpdf-compatible p {
            margin: 5px 0;
            line-height: 1.5;
        }

        .mpdf-compatible table {
            border-collapse: collapse;
            width: 100%;
        }

        .mpdf-compatible .no-print {
            display: none !important;
        }
    </style>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-6 border-b pb-2">
            <h3 class="text-lg font-semibold">Form Pembuatan Surat</h3>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('petugas.surat-template.store') }}" method="POST" class="space-y-4" id="suratForm">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <!-- Nomor Urut (Otomatis) -->
                <div class="flex gap-2">
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Nomor Urut (Otomatis)</label>
                        <input type="text" value="{{ $nomorUrut }}" readonly
                            class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100">
                        <input type="hidden" name="nomor_urut" value="{{ $nomorUrut }}">
                    </div>
                    <span class="text-gray-700 font-medium text-2xl mt-8">/</span>
                    <div>
                        <label for="format_surat" class="block text-gray-700 font-medium mb-1">Format Surat</label>
                        <input type="text" id="format_surat" name="format_surat" required
                            placeholder="MIS-0041-Rekom KKP/4/2025"
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('format_surat') }}">
                    </div>
                </div>

                <!-- Preview Nomor Surat -->
                <div class="mb-4 p-3 bg-blue-50 rounded border border-blue-200">
                    <p class="text-sm text-blue-700 font-semibold">Preview Nomor Surat:</p>
                    <p class="text-lg font-bold">{{ $nomorUrut }}/<span id="preview-format">[format]</span></p>
                </div>

                <div>
                    <label for="jenis_surat" class="block text-gray-700 font-medium mb-1">Jenis Surat</label>
                    <select class="w-full border border-gray-300 rounded px-3 py-2" id="jenis_surat" name="jenis_surat"
                        required>
                        <option value="">Pilih Jenis Surat</option>
                        @foreach ($jenisSuratList as $key => $value)
                            <option value="{{ $key }}" {{ old('jenis_surat') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="lampiran" class="block text-gray-700 font-medium mb-1">Lampiran</label>
                    <input type="text" id="lampiran" name="lampiran" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('lampiran') }}">
                </div>

                <div>
                    <label for="perihal" class="block text-gray-700 font-medium mb-1">Perihal</label>
                    <input type="text" id="perihal" name="perihal" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('perihal') }}">
                </div>

                <div>
                    <label for="tanggal" class="block text-gray-700 font-medium mb-1">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('tanggal', date('Y-m-d')) }}">
                </div>

                <div>
                    <label for="kepada" class="block text-gray-700 font-medium mb-1">Kepada</label>
                    <input type="text" id="kepada" name="kepada" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('kepada') }}">
                </div>
            </div>

            <!-- WYSIWYG Editor -->
            <div>
                <label for="isi_surat" class="wysiwyg-editor mpdf-compatible">Isi Surat</label>

                <!-- Advanced Table Controls -->
                <div class="table-controls" id="tableControls" style="display: none;">
                    <div>
                        <label>Baris: </label>
                        <input type="number" id="tableRows" value="3" min="1" max="20"
                            style="width: 50px;">
                    </div>
                    <div>
                        <label>Kolom: </label>
                        <input type="number" id="tableCols" value="3" min="1" max="20"
                            style="width: 50px;">
                    </div>
                    <div>
                        <label>Border: </label>
                        <select id="tableBorder">
                            <option value="1">Ada</option>
                            <option value="0">Tidak</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div>
                        <label>Alignment: </label>
                        <select id="tableAlign">
                            <option value="left">Kiri</option>
                            <option value="center">Tengah</option>
                            <option value="right">Kanan</option>
                        </select>
                    </div>
                    <div>
                        <label>Width: </label>
                        <input type="number" id="tableWidth" value="100" min="10" max="100"
                            style="width: 50px;"> %
                    </div>
                    <div>
                        <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600" type="button"
                            onclick="applyTableSettings()">Terapkan</button>
                        <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600" type="button"
                            onclick="insertCustomTable()">Buat Tabel</button>
                    </div>
                </div>

                <!-- Table Toolbar -->
                <div class="table-toolbar" id="tableToolbar" style="display: none;">
                    <button type="button" onclick="insertSimpleTable(3, 3)">Tabel 3x3</button>
                    <button type="button" onclick="insertSimpleTable(4, 4)">Tabel 4x4</button>
                    <button type="button" onclick="modifyTable('addRow')">+ Baris</button>
                    <button type="button" onclick="modifyTable('deleteRow')">- Baris</button>
                    <button type="button" onclick="modifyTable('addColumn')">+ Kolom</button>
                    <button type="button" onclick="modifyTable('deleteColumn')">- Kolom</button>
                    <button type="button" onclick="modifyTable('toggleBorder')">Toggle Border</button>
                    <button type="button" onclick="modifyTable('mergeCells')">Merge Sel</button>
                    <button type="button" onclick="modifyTable('splitCell')">Split Sel</button>
                    <button type="button" onclick="showTableControls()">⚙️ Settings</button>
                </div>

                <textarea id="isi_surat" name="isi_surat" class="w-full border border-gray-300 rounded" rows="15">
{!! old('isi_surat', '') !!}
</textarea>
            </div>

            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('petugas.surat-template.index') }}"
                    class="bg-[#F4B724] font-bold text-white px-4 py-2 rounded hover:bg-[#b88b22] transition">
                    ← Kembali
                </a>
                <button type="submit" id="submitBtn"
                    class="bg-[#17AD90] text-white font-semibold px-4 py-2 rounded hover:bg-[#136958] transition">
                    Simpan Surat
                </button>
            </div>
        </form>
    </div>

    <!-- Gunakan CKEditor 4 dengan plugin table lebih lengkap -->
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script src="https://cdn.ckeditor.com/4.22.1/standard/plugins/table/plugin.js"></script>

    <script>
        // Preview format surat
        document.getElementById('format_surat').addEventListener('input', function() {
            document.getElementById('preview-format').textContent = this.value || '[format]';
        });

        // Prevent default tab behavior pada seluruh form
        document.addEventListener('keydown', function(e) {
            // Jika fokus di CKEditor, biarkan CKEditor handle
            if (e.target.classList.contains('cke_editable') ||
                e.target.classList.contains('cke_contents')) {
                return;
            }

            // Jika fokus di textarea CKEditor (hidden)
            if (e.target.id === 'isi_surat') {
                e.preventDefault();
                editor.focus();
                return;
            }

            // Jika di field lain dalam form, allow normal tab
            if (e.key === 'Tab' && !e.ctrlKey && !e.altKey) {
                // Biarkan browser handle tab navigation untuk field lain
                return;
            }
        });

        // Fokus otomatis ke editor saat halaman load
        document.addEventListener('DOMContentLoaded', function() {
            // Tunggu CKEditor ready
            setTimeout(function() {
                if (editor) {
                    editor.focus();
                }
            }, 1000);
        });

        document.querySelector('textarea[name="isi_surat"]').addEventListener('input', function(e) {
            // Hapus tag img dari input
            this.value = this.value.replace(/<img[^>]*>/gi, '');
        });

        // Load plugin justify jika belum ada
        if (!CKEDITOR.plugins.registered.justify) {
            CKEDITOR.plugins.addExternal('justify', 'https://cdn.ckeditor.com/4.22.1/standard/plugins/justify/',
                'plugin.js');
        }
        // Debug: Cek plugins yang tersedia
        console.log('CKEDITOR plugins:', CKEDITOR.plugins.registered);
        var editor;
        var currentTable = null;

        // Inisialisasi CKEditor dengan config khusus untuk editing tabel
        CKEDITOR.plugins.addExternal('tableresize', '/js/ckeditor/plugins/tableresize/', 'plugin.js');
        //plugin justify
        CKEDITOR.plugins.addExternal('justify', '/js/ckeditor/plugins/justify/', 'plugin.js');
        CKEDITOR.replace('isi_surat', {
            height: 400,
            // Toolbar lengkap dengan table tools
            toolbar: [
                ['Source', '-', 'Preview'],
                ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
                ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat'],
                ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'],
                '/', //Baris Baru
                ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
                ['Table', 'HorizontalRule'],
                ['Styles', 'Format'],
                ['Maximize']
            ],
            // Enable table plugin dengan fitur lengkap
            extraPlugins: 'tableresize,tabletools,tab,justify',
            // // Konfigurasi untuk mempertahankan styling
            // allowedContent: true, // Izinkan semua konten
            // protectedSource: [
            //     /<table[^>]*>[\s\S]*?<\/table>/g, // Lindungi tag tabel
            //     /<p[^>]*>[\s\S]*?<\/p>/g, // Lindungi paragraf dengan atribut
            //     /<div[^>]*>[\s\S]*?<\/div>/g // Lindungi div dengan atribut
            // ],
            // Config table
            table: {
                contentToolbar: [
                    'tableColumn', 'tableRow', 'mergeTableCells',
                    'tableProperties', 'tableCellProperties'
                ],
                // Default table properties
                defaultProperties: {
                    border: 0,
                    cellPadding: 5,
                    cellSpacing: 0,
                    width: '100%',
                    borderStyle: 'none', // Tambahkan ini
                    borderColor: 'transparent' // Tambahkan ini
                },
                advanced: {
                    // Biarkan user mengatur border secara manual
                    border: {
                        allowed: true,
                        default: 0
                    },
                    // Izinkan border style
                    styles: 'border,border-collapse,border-color,border-style,border-width'
                }
            },
            // Izinkan semua atribut border
            allowedContent: true, // Izinkan semua konten
            protectedSource: [
                /<table[^>]*>[\s\S]*?<\/table>/g, // Lindungi tag tabel
                /<p[^>]*>[\s\S]*?<\/p>/g, // Lindungi paragraf dengan atribut
                /<div[^>]*>[\s\S]*?<\/div>/g // Lindungi div dengan atribut
            ],
            // Force plain text output
            basicEntities: false,
            entities: false,
            // Enter mode untuk surat resmi
            enterMode: CKEDITOR.ENTER_BR,
            shiftEnterMode: CKEDITOR.ENTER_P,
            // Auto paragraph OFF
            autoParagraph: false,
            // Tab navigation dalam tabel
            enableTabKeyTools: true,
            // Tab indentation (seperti Word)
            tabSpaces: 4, // 4 spasi per tab
            // Remove unnecessary plugins
            removePlugins: 'elementspath,resize',
            // Setup untuk clean output dan handling
            on: {
                instanceReady: function(ev) {
                    editor = ev.editor;
                    // setupTabBehavior();
                    // setupTableTracking();
                    // setupTabHandler();
                    // ===== OVERRIDE DEFAULT TABLE DIALOG =====
                    // Override dialog table untuk mengizinkan border="0"
                    editor.on('dialogDefinition', function(event) {
                        var dialogName = event.data.name;
                        var dialogDefinition = event.data.definition;

                        // Modifikasi dialog tabel
                        if (dialogName === 'table') {
                            var infoTab = dialogDefinition.getContents('info');

                            // Ubah default border ke 0
                            var borderField = infoTab.get('txtBorder');
                            if (borderField) {
                                borderField['default'] = '0'; // Set default ke 0
                            }

                            // Tambahkan pilihan border style
                            var borderStyleField = infoTab.get('selBorderStyle');
                            if (borderStyleField) {
                                borderStyleField['items'] = [
                                    ['None', 'none'],
                                    ['Solid', 'solid'],
                                    ['Dashed', 'dashed'],
                                    ['Dotted', 'dotted'],
                                    ['Double', 'double']
                                ];
                                borderStyleField['default'] = 'none';
                            }
                        }

                        // Modifikasi dialog table properties
                        if (dialogName === 'tableProperties') {
                            var infoTab = dialogDefinition.getContents('info');

                            // Set default border ke 0
                            var borderField = infoTab.get('txtBorder');
                            if (borderField) {
                                borderField['default'] = '0';
                            }
                        }
                    });
                    // Setup untuk mempertahankan atribut
                    editor.dataProcessor.htmlFilter.addRules({
                        elements: {
                            $: function(element) {
                                // Pastikan atribut alignment tidak dihapus
                                if (element.name === 'p' || element.name === 'div' ||
                                    element.name === 'td' || element.name === 'th') {
                                    if (element.attributes.align) {
                                        if (!element.attributes.style) {
                                            element.attributes.style = '';
                                        }
                                        element.attributes.style += 'text-align:' + element
                                            .attributes.align + ';';
                                    }
                                }

                                // Untuk tabel, pastikan border dan width
                                if (element.name === 'table') {
                                    if (!element.attributes.style) {
                                        element.attributes.style = '';
                                    }
                                    if (!element.attributes.style.includes('border-collapse')) {
                                        element.attributes.style += 'border-collapse: collapse;';
                                    }
                                }
                                return element;
                            }
                        }
                    });
                    // Setup untuk mempertahankan alignment
                    editor.on('getData', function(event) {
                        var data = event.data.dataValue;

                        // Convert align attribute to inline style
                        data = data.replace(/align="(left|center|right|justify)"/g, function(match,
                            align) {
                            return 'style="text-align:' + align + ';"';
                        });
                        // Preserve border="0" atau "none"
                        data = data.replace(/border="(0|none)"/g, function(match, borderValue) {
                            return 'border="' + borderValue +
                                '" style="border-collapse: collapse; border: none;"';
                        });

                        event.data.dataValue = data;
                    });
                    // Handle tab navigation dalam tabel
                    editor.on('key', function(event) {
                        if (event.data.keyCode === 9) { // Tab key
                            var selection = editor.getSelection();
                            var path = selection.getStartElement();
                            var table = path.getAscendant('table', true);

                            // Mencegah default behavior (keluar dari editor)
                            event.cancel();

                            if (table) {
                                // Handle tab navigation dalam tabel
                                var range = selection.getRanges()[0];
                                var td = range.startContainer.getAscendant('td', true) ||
                                    range.startContainer.getAscendant('th', true);

                                if (td) {
                                    moveToNextCell(td, !event.data.shiftKey);
                                }
                            } else {
                                // Jika di luar tabel, insert tab/spasi
                                if (event.data.shiftKey) {
                                    // Shift+Tab: outdent
                                    editor.execCommand('outdent');
                                } else {
                                    // Tab normal: indent atau insert tab character
                                    insertTabCharacter();
                                }
                            }
                            return false; // Prevent default
                        }
                    });

                    // Track current table selection
                    editor.on('selectionChange', function(evt) {
                        var selection = editor.getSelection();
                        var element = selection.getStartElement();
                        currentTable = element.getAscendant('table', true);

                        if (currentTable) {
                            document.getElementById('tableToolbar').style.display = 'flex';
                            document.getElementById('tableControls').style.display = 'none';
                            // Tambahkan custom resize handle
                            addCustomResizeHandle(currentTable);
                        } else {
                            document.getElementById('tableToolbar').style.display = 'none';
                            document.getElementById('tableControls').style.display = 'none';
                        }
                    });
                    // Setup tab character insertion
                    setupTabHandler();
                }
            }
        });

        // ===== FUNGSI UNTUK INSERT TAB CHARACTER =====
        function setupTabHandler() {
            if (!editor) return;
            // Add custom command untuk insert tab
            editor.addCommand('insertTab', {
                exec: function(editor) {
                    // Insert non-breaking spaces untuk tab
                    var tabSpaces = '&nbsp;&nbsp;&nbsp;&nbsp;'; // 4 spasi
                    editor.insertHtml(tabSpaces);
                }
            });

            // Add toolbar button untuk tab (optional)
            editor.ui.addButton('Tab', {
                label: 'Insert Tab',
                command: 'insertTab',
                toolbar: 'insert'
            });
        }

        function insertTabCharacter() {
            var selection = editor.getSelection();
            var ranges = selection.getRanges();

            // Cek apakah di dalam list
            var path = selection.getStartElement();
            var listItem = path.getAscendant('li', true);

            if (listItem) {
                // Jika di dalam list, gunakan indent/outdent
                if (event.shiftKey) {
                    editor.execCommand('outdent');
                } else {
                    editor.execCommand('indent');
                }
            } else {
                // Insert tab character (4 spasi)
                var tabSpaces = '\u00A0\u00A0\u00A0\u00A0'; // 4 non-breaking spaces
                editor.insertText(tabSpaces);
            }
        }


        // ===== FUNGSI UNTUK TAB NAVIGATION =====
        function moveToNextCell(currentCell, forward) {
            var table = currentCell.getAscendant('table', true);
            if (!table) return;

            var rows = table.$.rows;
            var currentRow = currentCell.getParent();
            var rowIndex = currentRow.$.rowIndex;
            var cellIndex = currentCell.$.cellIndex;

            if (forward) {
                // Move right
                if (cellIndex < currentRow.$.cells.length - 1) {
                    cellIndex++;
                } else {
                    // Move to next row
                    if (rowIndex < rows.length - 1) {
                        rowIndex++;
                        cellIndex = 0;
                    } else {
                        // At end of table, add new row
                        addRowToTable(table);
                        rowIndex++;
                        cellIndex = 0;
                    }
                }
            } else {
                // Move left (shift+tab)
                if (cellIndex > 0) {
                    cellIndex--;
                } else {
                    if (rowIndex > 0) {
                        rowIndex--;
                        cellIndex = rows[rowIndex].cells.length - 1;
                    }
                }
            }

            // Focus ke cell baru
            var newCell = rows[rowIndex].cells[cellIndex];
            var range = editor.createRange();
            range.selectNodeContents(newCell);
            range.collapse(true);
            editor.getSelection().selectRanges([range]);
            editor.focus();
        }

        function addRowToTable(table) {
            var lastRow = table.$.rows[table.$.rows.length - 1];
            var newRow = lastRow.cloneNode(true);

            // Clear content of new row
            for (var i = 0; i < newRow.cells.length; i++) {
                newRow.cells[i].innerHTML = '&nbsp;';
            }

            table.$.appendChild(newRow);
        }

        // ===== FUNGSI UNTUK EDITING TABEL =====
        function insertSimpleTable(rows, cols) {
            if (!editor) return;

            var tableHtml =
                '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
            for (var i = 0; i < rows; i++) {
                tableHtml += '<tr>';
                for (var j = 0; j < cols; j++) {
                    tableHtml += '<td>&nbsp;</td>';
                }
                tableHtml += '</tr>';
            }
            tableHtml += '</table>';

            editor.insertHtml(tableHtml);

            // Auto focus ke cell pertama
            setTimeout(function() {
                var range = editor.createRange();
                var firstCell = editor.document.getElementsByTag('td')[0];
                if (firstCell) {
                    range.selectNodeContents(firstCell);
                    range.collapse(true);
                    editor.getSelection().selectRanges([range]);
                    editor.focus();
                }
            }, 100);
        }

        function insertCustomTable() {
            var rows = parseInt(document.getElementById('tableRows').value) || 3;
            var cols = parseInt(document.getElementById('tableCols').value) || 3;
            var border = document.getElementById('tableBorder').value;
            var align = document.getElementById('tableAlign').value;
            var width = parseInt(document.getElementById('tableWidth').value) || 100;

            var borderAttr = '';
            var borderStyle = '';

            if (border === '1') {
                borderAttr = 'border="1"';
                borderStyle = 'border: 1px solid #000;';
            } else if (border === '0') {
                borderAttr = 'border="0"';
                borderStyle = 'border: none;';
            } else if (border === 'custom') {
                borderAttr = 'border="1"';
                borderStyle = 'border: 2px dashed #666;';
            }

            var tableHtml = '<table ' + borderAttr +
                ' cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: ' + width +
                '%; ' + borderStyle + '" align="' + align + '">';

            for (var i = 0; i < rows; i++) {
                tableHtml += '<tr>';
                for (var j = 0; j < cols; j++) {
                    tableHtml += '<td>&nbsp;</td>';
                }
                tableHtml += '</tr>';
            }
            tableHtml += '</table>';

            editor.insertHtml(tableHtml);
        }

        function modifyTable(action) {
            if (!editor || !currentTable) return;

            var selection = editor.getSelection();
            var ranges = selection.getRanges();
            var command = editor.getCommand('table' + action.charAt(0).toUpperCase() + action.slice(1));

            if (command) {
                command.exec();
            } else {
                // Custom actions
                switch (action) {
                    case 'addRow':
                        var lastRow = currentTable.$.rows[currentTable.$.rows.length - 1];
                        var newRow = lastRow.cloneNode(true);
                        for (var i = 0; i < newRow.cells.length; i++) {
                            newRow.cells[i].innerHTML = '&nbsp;';
                        }
                        currentTable.$.appendChild(newRow);
                        break;

                    case 'deleteRow':
                        var selectedCell = selection.getStartElement();
                        var row = selectedCell.getAscendant('tr', true);
                        if (row && currentTable.$.rows.length > 1) {
                            row.$.remove();
                        }
                        break;

                    case 'addColumn':
                        var rows = currentTable.$.rows;
                        for (var i = 0; i < rows.length; i++) {
                            var newCell = rows[i].insertCell(-1);
                            newCell.innerHTML = '&nbsp;';
                        }
                        break;

                    case 'deleteColumn':
                        var selectedCell = selection.getStartElement();
                        var cellIndex = selectedCell.$.cellIndex;
                        var rows = currentTable.$.rows;
                        for (var i = 0; i < rows.length; i++) {
                            if (rows[i].cells.length > 1) {
                                rows[i].deleteCell(cellIndex);
                            }
                        }
                        break;

                    case 'toggleBorder':
                        var currentBorder = currentTable.getAttribute('border') || '0';
                        if (currentBorder === '1') {
                            // Ubah ke no border
                            currentTable.removeAttribute('border');
                            currentTable.setAttribute('border', '0');
                            currentTable.setAttribute('style', 'border-collapse: collapse; border: none;');

                            // Hapus border dari semua cell
                            var rows = currentTable.$.rows;
                            for (var i = 0; i < rows.length; i++) {
                                for (var j = 0; j < rows[i].cells.length; j++) {
                                    rows[i].cells[j].style.border = 'none';
                                }
                            }
                        } else {
                            // Ubah ke border penuh
                            currentTable.setAttribute('border', '1');
                            currentTable.setAttribute('style', 'border-collapse: collapse; border: 1px solid #000;');

                            // Set border untuk semua cell
                            var rows = currentTable.$.rows;
                            for (var i = 0; i < rows.length; i++) {
                                for (var j = 0; j < rows[i].cells.length; j++) {
                                    rows[i].cells[j].style.border = '1px solid #000';
                                }
                            }
                        }
                        break;

                    case 'mergeCells':
                        editor.execCommand('mergeCells');
                        break;

                    case 'splitCell':
                        editor.execCommand('cellProperties');
                        break;
                }
            }

            editor.fire('change');
        }

        // Manual resize dengan input number
        function resizeTableManual() {
            if (!currentTable) {
                alert('Pilih tabel terlebih dahulu!');
                return;
            }

            var width = prompt('Masukkan lebar tabel (dalam % atau px, contoh: 80% atau 500px):',
                currentTable.getAttribute('width') || '100%');

            if (width) {
                currentTable.setAttribute('width', width);
                editor.fire('change');

                // Update resize handle position
                if (currentTable && resizeHandle) {
                    var tableRect = currentTable.$.getBoundingClientRect();
                    var editorRect = editor.container.$.getBoundingClientRect();

                    resizeHandle.style.left = (tableRect.right - editorRect.left - 10) + 'px';
                    resizeHandle.style.top = (tableRect.bottom - editorRect.top - 10) + 'px';
                }
            }
        }

        // Tambahkan button untuk manual resize di toolbar
        function addManualResizeButton() {
            var toolbar = document.getElementById('tableToolbar');
            if (toolbar) {
                var resizeButton = document.createElement('button');
                resizeButton.type = 'button';
                resizeButton.textContent = 'Resize';
                resizeButton.onclick = resizeTableManual;
                resizeButton.style.cssText =
                    'padding: 5px 10px; border: 1px solid #ccc; background: white; border-radius: 3px; cursor: pointer; font-size: 12px;';
                toolbar.appendChild(resizeButton);
            }
        }

        // Panggil setelah editor ready
        editor.on('instanceReady', function() {
            editor.on('beforeSetMode', function() {
                var data = editor.getData();
                // Hapus img dari data CKEditor
                data = data.replace(/<img[^>]*>/gi, '');
                editor.setData(data);
            });
            // Update visual border setiap kali ada perubahan
            editor.on('change', function() {
                updateBorderVisual();
            });
            // Fungsi untuk update visual border
            function updateBorderVisual() {
                var tables = editor.document.getElementsByTag('table');
                for (var i = 0; i < tables.count(); i++) {
                    var table = tables.getItem(i);
                    var border = table.getAttribute('border');

                    if (border === '0' || border === 'none') {
                        table.addClass('no-border-indicator');
                        table.setStyle('border', 'none');

                        // Update cells juga
                        var cells = table.$.getElementsByTagName('td');
                        var thCells = table.$.getElementsByTagName('th');

                        for (var j = 0; j < cells.length; j++) {
                            cells[j].style.border = 'none';
                        }
                        for (var j = 0; j < thCells.length; j++) {
                            thCells[j].style.border = 'none';
                        }
                    } else {
                        table.removeClass('no-border-indicator');
                    }
                }
            }

            // Panggil setup untuk border editing
            updateTableToolbar();
            setupBorderShortcuts();
            preserveBorderStyling();
            updateBorderVisual(); // Panggil pertama kali
            setTimeout(addManualResizeButton, 100);
        });

        function showTableControls() {
            document.getElementById('tableControls').style.display = 'flex';
            document.getElementById('tableToolbar').style.display = 'none';
        }

        function applyTableSettings() {
            if (!currentTable) return;

            var border = document.getElementById('tableBorder').value;
            var align = document.getElementById('tableAlign').value;
            var width = parseInt(document.getElementById('tableWidth').value) || 100;

            // Apply border
            if (border === '1') {
                currentTable.setAttribute('border', '1');
                currentTable.setAttribute('style', 'border-collapse: collapse; border: 1px solid #000;');

                // Set border untuk semua cell
                var rows = currentTable.$.rows;
                for (var i = 0; i < rows.length; i++) {
                    for (var j = 0; j < rows[i].cells.length; j++) {
                        rows[i].cells[j].style.border = '1px solid #000';
                    }
                }
            } else if (border === '0') {
                currentTable.setAttribute('border', '0');
                currentTable.setAttribute('style', 'border-collapse: collapse; border: none;');

                // Hapus border dari semua cell
                var rows = currentTable.$.rows;
                for (var i = 0; i < rows.length; i++) {
                    for (var j = 0; j < rows[i].cells.length; j++) {
                        rows[i].cells[j].style.border = 'none';
                    }
                }
            } else if (border === 'custom') {
                currentTable.setAttribute('border', '1');
                currentTable.setAttribute('style', 'border-collapse: collapse; border: 2px dashed #666;');

                // Set border untuk semua cell
                var rows = currentTable.$.rows;
                for (var i = 0; i < rows.length; i++) {
                    for (var j = 0; j < rows[i].cells.length; j++) {
                        rows[i].cells[j].style.border = '2px dashed #666';
                    }
                }
            }

            // Apply alignment
            currentTable.setAttribute('align', align);

            // Apply width
            currentTable.setAttribute('width', width + '%');

            editor.fire('change');
            document.getElementById('tableControls').style.display = 'none';
            document.getElementById('tableToolbar').style.display = 'flex';
        }

        // Fungsi untuk membersihkan HTML sebelum submit
        function cleanHTMLForSubmit(html) {
            // Decode HTML entities
            var temp = document.createElement('textarea');
            temp.innerHTML = html;
            html = temp.value;

            // Ganti multiple non-breaking spaces dengan single space
            html = html.replace(/\u00A0\u00A0\u00A0\u00A0/g, '    '); // 4 spasi untuk tab
            html = html.replace(/\u00A0\u00A0\u00A0/g, '   '); // 3 spasi
            html = html.replace(/\u00A0\u00A0/g, '  '); // 2 spasi
            html = html.replace(/\u00A0/g, ' '); // 1 spasi

            // Hapus komentar
            html = html.replace(/<!--[\s\S]*?-->/g, '');

            // Preserve border="0" dan border="none"
            html = html.replace(/<table([^>]*)>/g, function(match, attrs) {
                var result = match;

                // Jika ada border="0" atau border="none", pertahankan
                if (attrs.includes('border="0"') || attrs.includes('border="none"')) {
                    // Pastikan style juga mencerminkan no border
                    if (attrs.includes('style=')) {
                        if (!attrs.includes('border: none')) {
                            result = result.replace('style="', 'style="border: none; ');
                        }
                    } else {
                        result = result.replace('<table', '<table style="border: none;"');
                    }
                }

                return result;
            });

            // Hapus semua atribut kecuali yang diperlukan untuk tabel
            html = html.replace(/<(\w+)(\s[^>]*)?>/g, function(match, tagName, attributes) {
                var keepAttrs = [];

                if (tagName.toLowerCase() === 'table') {
                    keepAttrs = ['border', 'cellpadding', 'cellspacing', 'width', 'align', 'style'];
                } else if (tagName.toLowerCase() === 'td' || tagName.toLowerCase() === 'th') {
                    keepAttrs = ['rowspan', 'colspan'];
                }

                var newAttrs = '';
                if (attributes && keepAttrs.length > 0) {
                    var attrRegex = /(\w+)=["']([^"']*)["']/g;
                    var attrMatch;
                    while ((attrMatch = attrRegex.exec(attributes)) !== null) {
                        if (keepAttrs.includes(attrMatch[1].toLowerCase())) {
                            // Untuk style, hanya keep border-collapse
                            if (attrMatch[1].toLowerCase() === 'style') {
                                var styleValue = attrMatch[2];
                                if (styleValue.includes('border-collapse')) {
                                    newAttrs += ' style="border-collapse: collapse;"';
                                }
                            } else {
                                newAttrs += ' ' + attrMatch[1] + '="' + attrMatch[2] + '"';
                            }
                        }
                    }
                }

                return '<' + tagName + newAttrs + '>';
            });

            // Hapus style, class, lang, dir attributes lainnya
            html = html.replace(/\s+(style|class|lang|dir|id)="[^"]*"/gi, '');

            // Hapus style attributes yang tidak perlu
            html = html.replace(/ style="[^"]*"/gi, function(match) {
                if (match.includes('border-collapse')) {
                    return ' style="border-collapse: collapse;"';
                }
                return '';
            });

            // Hapus span dan div tags
            html = html.replace(/<\/?(span|div)[^>]*>/gi, '');

            // Hapus tag kosong
            html = html.replace(/<(\w+)[^>]*>\s*<\/\1>/gi, '');

            // Hapus multiple <br> tags
            html = html.replace(/(<br\s*\/?>\s*){3,}/gi, '<br><br>');

            // Normalize line breaks
            html = html.replace(/\r\n/g, '\n').replace(/\r/g, '\n');

            return html.trim();
        }

        // Keyboard shortcut helper
        function setupKeyboardShortcuts() {
            // Ctrl+Tab untuk insert tab khusus
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'Tab') {
                    e.preventDefault();
                    insertTabCharacter();
                }

                // Alt+Tab untuk toggle table navigation mode
                if (e.altKey && e.key === 'Tab') {
                    e.preventDefault();
                    toggleTableNavigationMode();
                }
            });

            // Pencegahan tab keluar dari editor
            editor.container.$.addEventListener('keydown', function(e) {
                if (e.key === 'Tab' && !e.ctrlKey && !e.altKey) {
                    // Biarkan CKEditor handle
                    return;
                }
            });
        }

        function toggleTableNavigationMode() {
            var isTableMode = editor.container.$.getAttribute('data-table-mode');
            if (isTableMode === 'true') {
                editor.container.$.setAttribute('data-table-mode', 'false');
                alert('Table navigation mode: OFF\nTab akan insert spasi');
            } else {
                editor.container.$.setAttribute('data-table-mode', 'true');
                alert('Table navigation mode: ON\nTab akan navigasi sel tabel');
            }
        }

        // Update table toolbar untuk tambahkan tab controls
        function updateTableToolbar() {
            var toolbar = document.getElementById('tableToolbar');
            if (!toolbar) return;

            // Cek apakah button sudah ada
            var existingTabBtn = toolbar.querySelector('[data-action="tab-control"]');
            if (!existingTabBtn) {
                var tabControl = document.createElement('div');
                tabControl.className = 'tab-controls';
                tabControl.style.cssText = 'display: flex; gap: 2px; margin-left: auto;';
                tabControl.innerHTML = `
            <button type="button" onclick="insertTabInCell()" title="Insert Tab in Cell (Ctrl+T)">
                ↹ Tab
            </button>
            <button type="button" onclick="toggleTabMode()" title="Toggle Tab Mode (Alt+Tab)">
                ⚙ Tab Mode
            </button>
        `;

                // Style untuk tab control buttons
                var buttons = tabControl.querySelectorAll('button');
                buttons.forEach(btn => {
                    btn.style.cssText = 'padding: 3px 6px; border: 1px solid #ccc; background: white; ' +
                        'border-radius: 2px; cursor: pointer; font-size: 11px; ' +
                        'font-family: monospace;';
                });

                toolbar.appendChild(tabControl);
            }
        }

        function insertTabInCell() {
            if (!currentTable) {
                // Jika tidak di tabel, insert tab biasa
                insertTabCharacter();
                return;
            }

            var selection = editor.getSelection();
            var range = selection.getRanges()[0];
            var td = range.startContainer.getAscendant('td', true) ||
                range.startContainer.getAscendant('th', true);

            if (td) {
                // Insert tab character dalam cell
                editor.insertText('\t');
            } else {
                insertTabCharacter();
            }
        }

        function toggleTabMode() {
            var currentMode = localStorage.getItem('ckeditorTabMode') || 'indent';
            var newMode = currentMode === 'indent' ? 'navigate' : 'indent';

            localStorage.setItem('ckeditorTabMode', newMode);

            if (newMode === 'indent') {
                alert('Tab Mode: Indent (Tab = spasi, Shift+Tab = outdent)');
            } else {
                alert('Tab Mode: Navigate (Tab = pindah sel, Shift+Tab = pindah mundur)');
            }
        }

        function setupTabBehavior() {
            if (!editor) return;
            // Override default tab behavior
            editor.on('key', function(event) {
                if (event.data.keyCode === 9) { // Tab key
                    event.cancel();

                    var selection = editor.getSelection();
                    var path = selection.getStartElement();
                    var table = path.getAscendant('table', true);
                    var tabMode = localStorage.getItem('ckeditorTabMode') || 'indent';

                    if (table && tabMode === 'navigate') {
                        // Navigasi dalam tabel
                        var range = selection.getRanges()[0];
                        var td = range.startContainer.getAscendant('td', true) ||
                            range.startContainer.getAscendant('th', true);

                        if (td) {
                            moveToNextCell(td, !event.data.shiftKey);
                        }
                    } else {
                        // Insert tab character atau indent/outdent
                        if (event.data.shiftKey) {
                            if (editor.commands.outdent) {
                                editor.execCommand('outdent');
                            }
                        } else {
                            // Cek jika di dalam list
                            var listItem = path.getAscendant('li', true);
                            if (listItem && editor.commands.indent) {
                                editor.execCommand('indent');
                            } else {
                                // Insert 4 spasi
                                editor.insertHtml('&nbsp;&nbsp;&nbsp;&nbsp;');
                            }
                        }
                    }
                    return false;
                }
            });
        }

        function setupTableTracking() {
            if (!editor) return;
            editor.on('selectionChange', function(evt) {
                var selection = editor.getSelection();
                var element = selection.getStartElement();
                currentTable = element.getAscendant('table', true);

                if (currentTable) {
                    document.getElementById('tableToolbar').style.display = 'flex';
                    document.getElementById('tableControls').style.display = 'none';
                    updateTableToolbar(); // Pastikan toolbar border muncul
                } else {
                    document.getElementById('tableToolbar').style.display = 'none';
                    document.getElementById('tableControls').style.display = 'none';
                }
            });
        }

        // Fungsi untuk memastikan atribut styling tidak hilang
        function ensureStylesPreserved() {
            if (!editor) return;

            editor.on('getData', function(event) {
                var data = event.data.dataValue;

                // Preserve alignment
                data = data.replace(/<([^>]+)align="([^"]+)"([^>]*)>/g, function(match, before, align, after) {
                    return '<' + before + 'style="text-align:' + align + ';"' + after + '>';
                });

                // Preserve table attributes
                data = data.replace(/<table([^>]*)>/g, function(match, attrs) {
                    // Pastikan tabel memiliki border collapse
                    if (!attrs.includes('style=')) {
                        return '<table' + attrs + ' style="border-collapse: collapse;">';
                    } else if (!attrs.includes('border-collapse')) {
                        return match.replace('style="', 'style="border-collapse: collapse; ');
                    }
                    return match;
                });

                event.data.dataValue = data;
            });
        }

        // ===== FUNGSI FORCE NO BORDER =====
        function forceNoBorder() {
            if (!currentTable) {
                alert('Pilih tabel terlebih dahulu!');
                return;
            }

            // Set border ke 0
            currentTable.setAttribute('border', '0');
            currentTable.setAttribute('style', 'border-collapse: collapse; border: none !important;');

            // Hapus border dari semua cell
            var rows = currentTable.$.rows;
            for (var i = 0; i < rows.length; i++) {
                for (var j = 0; j < rows[i].cells.length; j++) {
                    rows[i].cells[j].style.border = 'none !important';
                    rows[i].cells[j].removeAttribute('border');
                }
            }

            editor.fire('change');
            alert('Border telah dihapus!');
        }

        // ===== FUNGSI UNTUK EDIT BORDER TABEL =====
        function showBorderEditor() {
            if (!currentTable) {
                alert('Pilih tabel terlebih dahulu!');
                return;
            }

            // Ambil border style saat ini
            var currentBorder = currentTable.getAttribute('border') || '1';
            var currentStyle = currentTable.getAttribute('style') || '';
            var borderColor = '#000000';
            var borderWidth = '1px';
            var borderStyle = 'solid';

            // Parse current border style
            if (currentStyle.includes('border:')) {
                var borderMatch = currentStyle.match(/border:\s*([^;]+)/);
                if (borderMatch) {
                    var borderParts = borderMatch[1].split(/\s+/);
                    if (borderParts.length >= 3) {
                        borderWidth = borderParts[0];
                        borderStyle = borderParts[1];
                        borderColor = borderParts[2];
                    }
                }
            }

            // Dialog untuk edit border
            var borderDialog = `
        <div id="borderEditor" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;">
            <div style="background: white; padding: 20px; border-radius: 8px; width: 400px;">
                <h3 style="margin-top: 0;">Edit Border Tabel</h3>
                <div style="margin-bottom: 15px;">
                    <button onclick="forceNoBorder(); closeBorderEditor();" 
                        style="width: 100%; padding: 10px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; margin-bottom: 10px;">
                        ⛔ HAPUS SEMUA BORDER
                    </button>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Tipe Border:</label>
                    <select id="borderType" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="none">Tanpa Border</option>
                        <option value="full">Border Penuh</option>
                        <option value="outside">Border Luar Saja</option>
                        <option value="inside">Border Dalam Saja</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>

                <div id="customBorderOptions" style="display: none;">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px;">Border Style:</label>
                        <select id="borderStyle" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                            <option value="solid">Solid</option>
                            <option value="dashed">Dashed</option>
                            <option value="dotted">Dotted</option>
                            <option value="double">Double</option>
                            <option value="groove">Groove</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px;">Border Width:</label>
                        <select id="borderWidth" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                            <option value="1px">1px</option>
                            <option value="2px">2px</option>
                            <option value="3px">3px</option>
                            <option value="4px">4px</option>
                            <option value="5px">5px</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px;">Border Color:</label>
                        <input type="color" id="borderColor" value="${borderColor}" style="width: 100%; height: 40px;">
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; margin-top: 20px;">
                    <button onclick="applyBorderChanges()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Terapkan
                    </button>
                    <button onclick="closeBorderEditor()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    `;

            // Tambahkan dialog ke body
            document.body.insertAdjacentHTML('beforeend', borderDialog);

            // Set value berdasarkan border saat ini
            var borderTypeSelect = document.getElementById('borderType');
            if (currentBorder === '0' || currentBorder === 'none') {
                borderTypeSelect.value = 'none';
            } else {
                borderTypeSelect.value = 'full';
            }

            // Tampilkan custom options jika dipilih
            borderTypeSelect.addEventListener('change', function() {
                var customOptions = document.getElementById('customBorderOptions');
                customOptions.style.display = this.value === 'custom' ? 'block' : 'none';
            });

            // Set custom values
            document.getElementById('borderStyle').value = borderStyle;
            document.getElementById('borderWidth').value = borderWidth;
        }

        function applyBorderChanges() {
            if (!currentTable) return;

            var borderType = document.getElementById('borderType').value;
            var borderStyle = document.getElementById('borderStyle').value;
            var borderWidth = document.getElementById('borderWidth').value;
            var borderColor = document.getElementById('borderColor').value;

            // Reset semua border
            var rows = currentTable.$.rows;

            switch (borderType) {
                case 'none':
                    // Hapus semua border
                    currentTable.removeAttribute('border');
                    currentTable.setAttribute('style', 'border-collapse: collapse; border: none;');

                    // Hapus border dari semua cell
                    for (var i = 0; i < rows.length; i++) {
                        for (var j = 0; j < rows[i].cells.length; j++) {
                            rows[i].cells[j].style.border = 'none';
                        }
                    }
                    break;

                case 'full':
                    // Border penuh
                    currentTable.setAttribute('border', '1');
                    currentTable.setAttribute('style',
                        `border-collapse: collapse; border: ${borderWidth} ${borderStyle} ${borderColor};`);

                    // Set border untuk semua cell
                    for (var i = 0; i < rows.length; i++) {
                        for (var j = 0; j < rows[i].cells.length; j++) {
                            rows[i].cells[j].style.border = `${borderWidth} ${borderStyle} ${borderColor}`;
                            rows[i].cells[j].style.padding = '5px';
                        }
                    }
                    break;

                case 'outside':
                    // Hanya border luar tabel
                    currentTable.removeAttribute('border');
                    currentTable.setAttribute('style',
                        `border-collapse: collapse; 
                 border-top: ${borderWidth} ${borderStyle} ${borderColor};
                 border-bottom: ${borderWidth} ${borderStyle} ${borderColor};
                 border-left: ${borderWidth} ${borderStyle} ${borderColor};
                 border-right: ${borderWidth} ${borderStyle} ${borderColor};`
                    );

                    // Set border untuk cell pertama dan terakhir setiap baris
                    for (var i = 0; i < rows.length; i++) {
                        var cells = rows[i].cells;
                        for (var j = 0; j < cells.length; j++) {
                            cells[j].style.border = 'none';

                            // Border untuk cell luar
                            if (i === 0) cells[j].style.borderTop = `${borderWidth} ${borderStyle} ${borderColor}`;
                            if (i === rows.length - 1) cells[j].style.borderBottom =
                                `${borderWidth} ${borderStyle} ${borderColor}`;
                            if (j === 0) cells[j].style.borderLeft = `${borderWidth} ${borderStyle} ${borderColor}`;
                            if (j === cells.length - 1) cells[j].style.borderRight =
                                `${borderWidth} ${borderStyle} ${borderColor}`;

                            cells[j].style.padding = '5px';
                        }
                    }
                    break;

                case 'inside':
                    // Hanya border dalam sel
                    currentTable.removeAttribute('border');
                    currentTable.setAttribute('style', 'border-collapse: collapse; border: none;');

                    // Set border dalam untuk semua cell
                    for (var i = 0; i < rows.length; i++) {
                        for (var j = 0; j < rows[i].cells.length; j++) {
                            var cell = rows[i].cells[j];
                            cell.style.border = 'none';

                            // Border kanan dan bawah untuk cell yang bukan di tepi
                            if (j < rows[i].cells.length - 1) {
                                cell.style.borderRight = `${borderWidth} ${borderStyle} ${borderColor}`;
                            }
                            if (i < rows.length - 1) {
                                cell.style.borderBottom = `${borderWidth} ${borderStyle} ${borderColor}`;
                            }

                            cell.style.padding = '5px';
                        }
                    }
                    break;

                case 'custom':
                    // Custom border berdasarkan input
                    currentTable.removeAttribute('border');
                    currentTable.setAttribute('style',
                        `border-collapse: collapse; 
                 border: ${borderWidth} ${borderStyle} ${borderColor};`
                    );

                    // Terapkan ke semua cell
                    for (var i = 0; i < rows.length; i++) {
                        for (var j = 0; j < rows[i].cells.length; j++) {
                            rows[i].cells[j].style.border = `${borderWidth} ${borderStyle} ${borderColor}`;
                            rows[i].cells[j].style.padding = '5px';
                        }
                    }
                    break;
            }

            closeBorderEditor();
            editor.fire('change');
        }

        function closeBorderEditor() {
            var editor = document.getElementById('borderEditor');
            if (editor) {
                editor.remove();
            }
        }

        function applyBorderPreset(presetName) {
            if (!currentTable) return;

            var presets = {
                'none': {
                    tableStyle: 'border-collapse: collapse; border: none;',
                    cellBorder: 'none'
                },
                'thin': {
                    tableStyle: 'border-collapse: collapse; border: 1px solid #000;',
                    cellBorder: '1px solid #000'
                },
                'thick': {
                    tableStyle: 'border-collapse: collapse; border: 3px solid #333;',
                    cellBorder: '3px solid #333'
                },
                'dashed': {
                    tableStyle: 'border-collapse: collapse; border: 2px dashed #666;',
                    cellBorder: '2px dashed #666'
                },
                'grid': {
                    tableStyle: 'border-collapse: collapse;',
                    cellBorder: '1px solid #ccc'
                }
            };

            if (presets[presetName]) {
                currentTable.setAttribute('style', presets[presetName].tableStyle);

                var rows = currentTable.$.rows;
                for (var i = 0; i < rows.length; i++) {
                    for (var j = 0; j < rows[i].cells.length; j++) {
                        rows[i].cells[j].style.border = presets[presetName].cellBorder;
                        rows[i].cells[j].style.padding = '5px';
                    }
                }

                editor.fire('change');
            }
        }

        // ===== FUNGSI QUICK BORDER TOGGLE =====
        function toggleTableBorder() {
            if (!currentTable) return;

            var currentBorder = currentTable.getAttribute('border');
            if (currentBorder === '1') {
                // Ubah ke no border
                currentTable.removeAttribute('border');
                currentTable.setAttribute('style', 'border-collapse: collapse; border: none;');

                // Hapus border dari semua cell
                var rows = currentTable.$.rows;
                for (var i = 0; i < rows.length; i++) {
                    for (var j = 0; j < rows[i].cells.length; j++) {
                        rows[i].cells[j].style.border = 'none';
                    }
                }
            } else {
                // Ubah ke border penuh
                currentTable.setAttribute('border', '1');
                currentTable.setAttribute('style', 'border-collapse: collapse; border: 1px solid #000;');

                // Set border untuk semua cell
                var rows = currentTable.$.rows;
                for (var i = 0; i < rows.length; i++) {
                    for (var j = 0; j < rows[i].cells.length; j++) {
                        rows[i].cells[j].style.border = '1px solid #000';
                    }
                }
            }

            editor.fire('change');
        }

        // ===== FUNGSI EDIT BORDER INDIVIDUAL SEL =====
        function editCellBorders() {
            if (!currentTable) {
                alert('Pilih tabel terlebih dahulu!');
                return;
            }

            var selection = editor.getSelection();
            var range = selection.getRanges()[0];
            var selectedCell = range.startContainer.getAscendant('td', true) ||
                range.startContainer.getAscendant('th', true);

            if (!selectedCell) {
                alert('Pilih sel terlebih dahulu!');
                return;
            }

            var cellDialog = `
        <div id="cellBorderEditor" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;">
            <div style="background: white; padding: 20px; border-radius: 8px; width: 350px;">
                <h3 style="margin-top: 0;">Edit Border Sel</h3>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 10px;">
                        <input type="checkbox" id="topBorder" checked> Border Atas
                    </label>
                    <label style="display: block; margin-bottom: 10px;">
                        <input type="checkbox" id="rightBorder" checked> Border Kanan
                    </label>
                    <label style="display: block; margin-bottom: 10px;">
                        <input type="checkbox" id="bottomBorder" checked> Border Bawah
                    </label>
                    <label style="display: block; margin-bottom: 15px;">
                        <input type="checkbox" id="leftBorder" checked> Border Kiri
                    </label>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px;">Style:</label>
                    <select id="cellBorderStyle" style="width: 100%; padding: 8px;">
                        <option value="solid">Solid</option>
                        <option value="dashed">Dashed</option>
                        <option value="dotted">Dotted</option>
                    </select>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px;">Width:</label>
                    <input type="number" id="cellBorderWidth" value="1" min="1" max="5" style="width: 100%; padding: 8px;">
                </div>

                <div style="display: flex; justify-content: space-between;">
                    <button onclick="applyCellBorderChanges()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Terapkan
                    </button>
                    <button onclick="closeCellBorderEditor()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    `;

            document.body.insertAdjacentHTML('beforeend', cellDialog);
        }

        function applyCellBorderChanges() {
            var selection = editor.getSelection();
            var range = selection.getRanges()[0];
            var selectedCell = range.startContainer.getAscendant('td', true) ||
                range.startContainer.getAscendant('th', true);

            if (!selectedCell) return;

            var style = document.getElementById('cellBorderStyle').value;
            var width = document.getElementById('cellBorderWidth').value + 'px';

            // Apply borders berdasarkan checkbox
            if (document.getElementById('topBorder').checked) {
                selectedCell.$.style.borderTop = `${width} ${style} #000`;
            } else {
                selectedCell.$.style.borderTop = 'none';
            }

            if (document.getElementById('rightBorder').checked) {
                selectedCell.$.style.borderRight = `${width} ${style} #000`;
            } else {
                selectedCell.$.style.borderRight = 'none';
            }

            if (document.getElementById('bottomBorder').checked) {
                selectedCell.$.style.borderBottom = `${width} ${style} #000`;
            } else {
                selectedCell.$.style.borderBottom = 'none';
            }

            if (document.getElementById('leftBorder').checked) {
                selectedCell.$.style.borderLeft = `${width} ${style} #000`;
            } else {
                selectedCell.$.style.borderLeft = 'none';
            }

            closeCellBorderEditor();
            editor.fire('change');
        }

        function closeCellBorderEditor() {
            var editor = document.getElementById('cellBorderEditor');
            if (editor) {
                editor.remove();
            }
        }

        // ===== UPDATE TABLE TOOLBAR =====
        function updateTableToolbar() {
            var toolbar = document.getElementById('tableToolbar');
            if (!toolbar) return;

            // Cek apakah button border sudah ada
            var existingBorderBtn = toolbar.querySelector('[data-action="border-edit"]');
            if (!existingBorderBtn) {
                // Tambahkan border control buttons
                var borderControls = document.createElement('div');
                borderControls.className = 'border-controls';
                borderControls.style.cssText =
                    'display: flex; gap: 2px; margin-left: 10px; border-left: 1px solid #ccc; padding-left: 10px;';
                borderControls.innerHTML = `
            <button type="button" onclick="forceNoBorder()" title="Hapus Semua Border" style="background: #dc3545; color: white;">
                ⛔ No Border
            </button>
            <button type="button" onclick="showBorderEditor()" title="Edit Border Tabel (Ctrl+B)" data-action="border-edit">
                🏁 Border
            </button>
            <button type="button" onclick="toggleTableBorder()" title="Toggle Border On/Off">
                ⚡ Toggle
            </button>
            <button type="button" onclick="editCellBorders()" title="Edit Border Sel">
                📄 Sel
            </button>
        `;

                // Style untuk border control buttons
                var buttons = borderControls.querySelectorAll('button');
                buttons.forEach(btn => {
                    btn.style.cssText = 'padding: 3px 6px; border: 1px solid #ccc; ' +
                        'border-radius: 2px; cursor: pointer; font-size: 11px; ' +
                        'font-family: monospace; min-width: 60px; margin: 2px;';
                });

                toolbar.appendChild(borderControls);
            }
        }

        // ===== SHORTCUT KEYBOARD =====
        function setupBorderShortcuts() {
            document.addEventListener('keydown', function(e) {
                // Ctrl+B untuk edit border
                if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                    e.preventDefault();
                    if (currentTable) {
                        showBorderEditor();
                    }
                }

                // Ctrl+Shift+B untuk toggle border
                if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'b') {
                    e.preventDefault();
                    if (currentTable) {
                        toggleTableBorder();
                    }
                }
            });
        }

        // ===== PRESERVE BORDER STYLING =====
        function preserveBorderStyling() {
            editor.on('getData', function(event) {
                var data = event.data.dataValue;

                // Pastikan border styling tidak hilang
                data = data.replace(/<table([^>]*)>/g, function(match, attrs) {
                    var result = match;

                    // Jika ada style border, pastikan dipertahankan
                    if (attrs.includes('style=')) {
                        // Normalize border style
                        if (attrs.includes('border: none')) {
                            result = result.replace('border: none', 'border: none');
                        } else if (attrs.includes('border: ')) {
                            // Keep existing border
                        }
                    }

                    return result;
                });

                // Preserve cell border styles
                data = data.replace(/<(td|th)([^>]*)>/g, function(match, tag, attrs) {
                    if (attrs.includes('style=')) {
                        // Preserve border properties
                        var borderRegex = /border[^:]*:\s*[^;]+/gi;
                        var borders = attrs.match(borderRegex);
                        if (borders) {
                            // Keep border styles
                        }
                    }
                    return match;
                });

                event.data.dataValue = data;
            });
        }

        // Fungsi untuk memformat tabel sebelum submit
        function formatTableForPDF() {
            if (!editor) return;

            var data = editor.getData();

            // Tambahkan style untuk semua elemen dengan align
            data = data.replace(/<(\w+)\s+([^>]*\s)?align="([^"]+)"([^>]*)>/g,
                '<$1 $2 style="text-align:$3;" $4>');

            // Pastikan tabel memiliki atribut yang benar
            data = data.replace(/<table[^>]*>/gi, function(match) {
                var result = match;

                // Tambahkan border jika belum ada
                if (!result.includes('border=')) {
                    result = result.replace('<table', '<table border="1"');
                }

                // Tambahkan cellpadding jika belum ada
                if (!result.includes('cellpadding=')) {
                    result = result.replace('<table', '<table cellpadding="5"');
                }

                // Tambahkan cellspacing jika belum ada
                if (!result.includes('cellspacing=')) {
                    result = result.replace('<table', '<table cellspacing="0"');
                }

                // Tambahkan style border-collapse jika belum ada
                if (!result.includes('border-collapse')) {
                    if (result.includes('style=')) {
                        result = result.replace('style="', 'style="border-collapse: collapse; ');
                    } else {
                        result = result.replace('<table', '<table style="border-collapse: collapse;"');
                    }
                }

                return result;
            });

            // Pastikan td/th memiliki style border
            data = data.replace(/<(td|th)([^>]*)>/gi, function(match, tag, attrs) {
                if (attrs.includes('style=')) {
                    if (!attrs.includes('border:')) {
                        return '<' + tag + attrs.replace('style="', 'style="border: 1px solid #000; ');
                    }
                } else {
                    return '<' + tag + attrs + ' style="border: 1px solid #000; padding: 5px;">';
                }
                return match;
            });

            editor.setData(data);
        }

        // Panggil sebelum form submit
        document.getElementById('suratForm').addEventListener('submit', function(e) {
            if (editor) {
                // Format tabel terlebih dahulu
                // ensureTableAttributes();
                formatTableForPDF();

                editor.updateElement();

                // Bersihkan HTML sebelum submit
                var textarea = document.getElementById('isi_surat');
                var html = textarea.value;

                // Convert align attribute to style
                html = html.replace(/align="(left|center|right|justify)"/g, function(match, align) {
                    return 'style="text-align:' + align + ';"';
                });

                // Hapus img dan script
                html = html.replace(/<img[^>]*>/gi, '');
                html = html.replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '');

                // Ensure tables have proper styling for PDF
                html = html.replace(/<table([^>]*)>/g, function(match, attrs) {
                    var result = match;

                    // Add border-collapse if not present
                    if (!result.includes('border-collapse')) {
                        if (result.includes('style=')) {
                            result = result.replace('style="', 'style="border-collapse: collapse; ');
                        } else {
                            result = result.replace('<table', '<table style="border-collapse: collapse;"');
                        }
                    }

                    return result;
                });

                textarea.value = html;
            }
            return true;
        });
        // Panggil setup keyboard shortcuts
        setupKeyboardShortcuts();

        // Shortcut untuk insert table dengan Ctrl+T
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 't') {
                e.preventDefault();
                insertSimpleTable(3, 3);
            }
        });
    </script>
@endsection
