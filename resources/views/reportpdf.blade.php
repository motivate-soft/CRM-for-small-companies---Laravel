<head>
    <style>

        h1 {
            margin-top: 30px;
        }

        table {
            width: 100%;
            margin: auto;
            margin-top: 30px;
            border:1px solid black;
            border-collapse:collapse;
        }

        .table1 tr {
            border: 1px solid black;
        }

        .table1 td {
            border: 1px solid black;
            width: 50%;
            padding-left: 5px;
            position: relative;
        }

        .table1 span {
            position: absolute;
            left: 150px;
        }

        .table2 td {
            border: 1px solid black;
            text-align: center;
        }

    </style>
</head>
<body>
<div style="width: 100%">
    <h1 style="text-align: center">Listado Resumen mensual del registro de jornada (detalle horario)</h1>
    <table class="table1">
        <tbody>
        <tr>
            <td>Empresa:<span>BIODACTIL, S.L.</span></td>
            <td>Trabajador:<span>{{$name}}</span></td>
        </tr>
        <tr>
            <td>C.I.F./N.I.F.:<span>{{$cif}}</span></td>
            <td>N.I.F.:<span>{{$nif}}</span></td>
        </tr>
        <tr>
            <td>Centro de Trabajo:<span>BIODACTIL, S.L.</span></td>
            <td>Nº Afiliación:<span>{{$affiliation}}</span></td>
        </tr>
        <tr>
            <td>C.C.C.:<span>28/1713370/53</span></td>
            <td>Mes y Año:<span>{{$month}}/{{$year}}</span></td>
        </tr>
        </tbody>
    </table>
    <table class="table2">
        <thead>
        <tr>
            <td rowspan="2">DIA</td>
            <td colspan="2">MAÑANAS</td>
            <td colspan="2">TARDES</td>
            <td rowspan="2">HORAS<br>ORDINARIAS</td>
            <td rowspan="2">HORAS EXTRAOR./<br>COMPLEMENTARIAS</td>
            <td rowspan="2">FIRMA DEL<br>TRABAJADOR / A</td>
        </tr>
        <tr>
            <td>ENTRADA</td>
            <td>SALIDA</td>
            <td>ENTRADA</td>
            <td>SALIDA</td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>1</td>
            <td>1</td>
            <td>1</td>
            <td>1</td>
            <td>1</td>
            <td>1</td>
            <td>1</td>
            <td>1</td>
        </tr>
        </tbody>
    </table>
</div>
</body>

