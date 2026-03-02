<!DOCTYPE html>
<html>
<head>
    <title>Cetak Label Barang TnJ 108</title>
    <style>
        @page { 
            margin: 0; 
        }
        body {
            margin: 0;
            width: 210mm;
            height: 167mm;
            font-family: Arial, sans-serif;
            background-color: #eef4ab;
        }

        table {
            border-collapse: separate; 
            border-spacing: 2mm 2mm; 
            margin: 0 auto;
        }

        td {
            width: 38mm;   
            height: 18mm;  
            border: 0.1pt solid #ccc; 
            vertical-align: middle;
            text-align: center;
            box-sizing: border-box;
            overflow: hidden;
            background-color: white;
            border-radius: 5px;
        }

        .empty-cell {
            border: 0.1pt solid #f2f2f2;
        }

        .nama {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.1;
            margin-bottom: 2px;
            max-height: 8mm
            word-wrap: break-word; 
        }

        .harga {
            font-size: 10pt;
            font-weight: bold;
            display: block;
        }

        .id {
            font-size: 6pt;
            color: #666;
            margin-top: 4px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <table>
        @php
            $itemIndex = 0;
        @endphp

        {{-- Loop 8 Baris --}}
        @for ($row = 0; $row < 8; $row++)
            <tr>
                {{-- Loop 5 kolom --}}
                @for ($col = 0; $col < 5; $col++)
                    @php 
                        $currentCell = ($row * 5) + $col; 
                    @endphp

                    @if ($currentCell >= $startIndex && $itemIndex < count($barang))
                        <td>
                            <div class="nama">{{ $barang[$itemIndex]->nama }}</div>
                            <div class="harga">Rp {{ number_format($barang[$itemIndex]->harga, 0, ',', '.') }}</div>
                            <div class="id">{{ str_pad($barang[$itemIndex]->id_barang, 4, '0', STR_PAD_LEFT) }}</div>
                            @php $itemIndex++; @endphp
                        </td>
                    @else
                        <td class="empty-cell">&nbsp;</td>
                    @endif
                @endfor
            </tr>
        @endfor
    </table>

</body>
</html>