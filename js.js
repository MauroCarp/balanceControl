
function getUrlAfterAdmin() {
    const url = window.location.pathname;
    const index = url.indexOf('admin/');
    if (index !== -1) {
        return url.substring(index + 'admin/'.length);
    }
    return '';
}

let getMermaHumedad = () => {

    fetch('/merma-humedad', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            cereal: document.getElementById('cereal').value,
            humedad: Number(document.getElementById('humedad').value)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.merma !== undefined) {
            document.getElementById('mermaHumedad').value = data.merma;

            let pesoNeto = Number(document.getElementById('pesoNeto').value);

            let resultado = (pesoNeto - (pesoNeto * (data.merma / 100)));

            document.getElementById('pesoNetoHumedad').value = resultado;
        } else {
            console.error('No se encontró el valor de merma.');
        }
    })
    .catch(error => {
        console.error('Error al consultar la base de datos:', error);
    });

}
///*********************     
//                          INDEX        
//                                   ************************/

if(getUrlAfterAdmin() === '') {

    setTimeout(() => {

        let btnBarlovento = document.getElementById('btnBarlovento')
        let btnPaihuen = document.getElementById('btnPaihuen')

        btnBarlovento.addEventListener('click', function() {
            console.log('asdasdasd')
            btnBarlovento.style.display = 'none';
            btnPaihuen.style.display = 'none';

            let btnsBarlovento = document.getElementById('btnsBarlovento');

            btnsBarlovento.style.display = 'block';
        })

    }, 2500);
}

///*********************     
//                          CREATE INGRESOS       
//                                   ************************/

if(getUrlAfterAdmin() === 'barlovento-ingresos/create' || (getUrlAfterAdmin().split('/')[2] === 'edit' && (getUrlAfterAdmin().split('/')[0] === 'barlovento-ingresos'))) {

    let origen_terneros = document.getElementById('origen_terneros')
    let origen_terneras = document.getElementById('origen_terneras')
    
    origen_terneros.addEventListener('change', function() {
            let resultado = Number(origen_terneros.value) + Number(origen_terneras.value);
            document.getElementById('cantidadTotal').value = resultado;
    })

    origen_terneras.addEventListener('change', function() {
        let resultado = Number(origen_terneros.value) + Number(origen_terneras.value);
        document.getElementById('cantidadTotal').value = resultado;
    })

    let origen_pesoBruto = document.getElementById('origen_pesoBruto')
    let origen_pesoNeto = document.getElementById('origen_pesoNeto')
    let origen_desbaste = document.getElementById('origen_desbaste')

    origen_pesoBruto.addEventListener('change', function() {
            let resultado = Number(origen_pesoBruto.value) - Number(origen_pesoNeto.value);
            document.getElementById('diferencia').value = resultado;
    })

    origen_pesoNeto.addEventListener('change', function() {

        let resultado = Number(origen_pesoBruto.value) - Number(origen_pesoNeto.value);
        document.getElementById('diferencia').value = resultado;

        if(origen_desbaste.value != '') {
            
            let resultado = (Number(origen_pesoNeto.value) - (Number(origen_pesoNeto.value) * (Number(origen_desbaste.value) / 100)));

            document.getElementById('pesoDesbaste').value = resultado;
        }

    })

    origen_desbaste.addEventListener('change', function() {

        if(origen_desbaste.value != '') {

            let resultado = (Number(origen_pesoNeto.value) - (Number(origen_pesoNeto.value) * (Number(origen_desbaste.value) / 100)));

            document.getElementById('pesoDesbaste').value = resultado;
        }

    })

    let destino_terneros = document.getElementById('destino_terneros')
    let destino_terneras = document.getElementById('destino_terneras')

    destino_terneros.addEventListener('change', function() {
            let resultado = Number(destino_terneros.value) + Number(destino_terneras.value);
            document.getElementById('cantidadTotalDestino').value = resultado;
    })

    destino_terneras.addEventListener('change', function() {
        let resultado = Number(destino_terneros.value) + Number(destino_terneras.value);
        document.getElementById('cantidadTotalDestino').value = resultado;
    })

    let destino_pesoBruto = document.getElementById('destino_pesoBruto')
    let destino__tara = document.getElementById('destino_tara')

    destino_pesoBruto.addEventListener('change', function() {
            let resultado = Number(destino_pesoBruto.value) - Number(destino__tara.value);
            document.getElementById('destino_diferencia').value = resultado;
    })

    destino__tara.addEventListener('change', function() {
        let resultado = Number(destino_pesoBruto.value) - Number(destino__tara.value);
        document.getElementById('destino_diferencia').value = resultado;

    })

    if(getUrlAfterAdmin().split('/')[2] === 'edit' && (getUrlAfterAdmin().split('/')[0] === 'barlovento-ingresos')) {
    
        setTimeout(() => {
        let resultado = Number(origen_terneros.value) + Number(origen_terneras.value);
        document.getElementById('cantidadTotal').value = resultado;

        resultado = Number(origen_pesoBruto.value) - Number(origen_pesoNeto.value);
        document.getElementById('diferencia').value = resultado;

        resultado = (Number(origen_pesoNeto.value) - (Number(origen_pesoNeto.value) * (Number(origen_desbaste.value) / 100)));
        document.getElementById('pesoDesbaste').value = resultado;

        resultado = Number(destino_terneros.value) + Number(destino_terneras.value);
        document.getElementById('cantidadTotalDestino').value = resultado;
        
        resultado = Number(destino_pesoBruto.value) - Number(destino__tara.value);
        document.getElementById('destino_diferencia').value = resultado;
        }, 1000);
    }

}

///*********************     
//                          CREATE EGRESOS       
//                                   ************************/

if(getUrlAfterAdmin() === 'barlovento-egresos/create') {

    let novillos = document.getElementById('novillos')
    let vaquillonas = document.getElementById('vaquillonas')
    
    novillos.addEventListener('change', function() {
            let resultado = Number(novillos.value) + Number(vaquillonas.value);
            document.getElementById('cantidad').value = resultado;
    })

    vaquillonas.addEventListener('change', function() {
        let resultado = Number(novillos.value) + Number(vaquillonas.value);
        document.getElementById('cantidad').value = resultado;
    })

    let pesoBruto = document.getElementById('pesoBruto')
    let tara = document.getElementById('pesoTara')

    pesoBruto.addEventListener('change', function() {
            let resultado = Number(pesoBruto.value) - Number(tara.value);
            document.getElementById('pesoNeto').value = resultado;
    })

    tara.addEventListener('change', function() {
        let resultado = Number(pesoBruto.value) - Number(tara.value);
        document.getElementById('pesoNeto').value = resultado;

        // let resultadoDesbaste = (Number(origen_pesoNeto.value) - (Number(origen_pesoNeto.value) * (Number(origen_desbaste.value) / 100)));

        // document.getElementById('pesoNetoDesbastado').value = resultadoDesbaste;

    })


}

///*********************     
//                          CREATE CEREALES       
//                                   ************************/

if(getUrlAfterAdmin() === 'barlovento-cereales/create' || getUrlAfterAdmin() === 'paihuen-cereales/create') {

    let pesoBruto = document.getElementById('pesoBruto')
    let tara = document.getElementById('pesoTara')

    pesoBruto.addEventListener('change', function() {
        let resultado = Number(pesoBruto.value) - Number(tara.value);
            document.getElementById('pesoNeto').value = resultado;
            getMermaHumedad()


    })

    tara.addEventListener('change', function() {

        let resultado = Number(pesoBruto.value) - Number(tara.value);
        document.getElementById('pesoNeto').value = resultado;
        getMermaHumedad()


    })


    let humedad = document.getElementById('humedad')

    humedad.addEventListener('change', function() {
            getMermaHumedad()
    })






}
///*********************     
//                          EDIT INGRESOS
//                                   ************************/

///*********************     
//                          EDIT CEREALES       
//                                   ************************/


if(getUrlAfterAdmin().split('/')[2] === 'edit' && (getUrlAfterAdmin().split('/')[0] === 'barlovento-cereales' || getUrlAfterAdmin().split('/')[0] === 'paihuen-cereales')) {

    
    let pesoBruto = document.getElementById('pesoBruto')
    let tara = document.getElementById('pesoTara')

    setTimeout(() => {

        let resultado = Number(pesoBruto.value) - Number(tara.value);

        document.getElementById('pesoNeto').value = resultado;

        getMermaHumedad()

    }, 1000);

    pesoBruto.addEventListener('change', function() {
        let resultado = Number(pesoBruto.value) - Number(tara.value);
            document.getElementById('pesoNeto').value = resultado;

    })

    tara.addEventListener('change', function() {

        let resultado = Number(pesoBruto.value) - Number(tara.value);
        document.getElementById('pesoNeto').value = resultado;

    })


    let humedad = document.getElementById('humedad')

    humedad.addEventListener('change', function() {
         
            getMermaHumedad()

    })

}




