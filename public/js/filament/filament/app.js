(()=>{var Z=Object.create,L=Object.defineProperty,ee=Object.getPrototypeOf,te=Object.prototype.hasOwnProperty,re=Object.getOwnPropertyNames,ne=Object.getOwnPropertyDescriptor,ae=o=>L(o,"__esModule",{value:!0}),ie=(o,n)=>()=>(n||(n={exports:{}},o(n.exports,n)),n.exports),se=(o,n,p)=>{if(n&&typeof n=="object"||typeof n=="function")for(let d of re(n))!te.call(o,d)&&d!=="default"&&L(o,d,{get:()=>n[d],enumerable:!(p=ne(n,d))||p.enumerable});return o},oe=o=>se(ae(L(o!=null?Z(ee(o)):{},"default",o&&o.__esModule&&"default"in o?{get:()=>o.default,enumerable:!0}:{value:o,enumerable:!0})),o),fe=ie((o,n)=>{(function(p,d,P){if(!p)return;for(var h={8:"backspace",9:"tab",13:"enter",16:"shift",17:"ctrl",18:"alt",20:"capslock",27:"esc",32:"space",33:"pageup",34:"pagedown",35:"end",36:"home",37:"left",38:"up",39:"right",40:"down",45:"ins",46:"del",91:"meta",93:"meta",224:"meta"},y={106:"*",107:"+",109:"-",110:".",111:"/",186:";",187:"=",188:",",189:"-",190:".",191:"/",192:"`",219:"[",220:"\\",221:"]",222:"'"},g={"~":"`","!":"1","@":"2","#":"3",$:"4","%":"5","^":"6","&":"7","*":"8","(":"9",")":"0",_:"-","+":"=",":":";",'"':"'","<":",",">":".","?":"/","|":"\\"},q={option:"alt",command:"meta",return:"enter",escape:"esc",plus:"+",mod:/Mac|iPod|iPhone|iPad/.test(navigator.platform)?"meta":"ctrl"},S,w=1;w<20;++w)h[111+w]="f"+w;for(w=0;w<=9;++w)h[w+96]=w.toString();function O(e,t,a){if(e.addEventListener){e.addEventListener(t,a,!1);return}e.attachEvent("on"+t,a)}function T(e){if(e.type=="keypress"){var t=String.fromCharCode(e.which);return e.shiftKey||(t=t.toLowerCase()),t}return h[e.which]?h[e.which]:y[e.which]?y[e.which]:String.fromCharCode(e.which).toLowerCase()}function $(e,t){return e.sort().join(",")===t.sort().join(",")}function B(e){var t=[];return e.shiftKey&&t.push("shift"),e.altKey&&t.push("alt"),e.ctrlKey&&t.push("ctrl"),e.metaKey&&t.push("meta"),t}function V(e){if(e.preventDefault){e.preventDefault();return}e.returnValue=!1}function H(e){if(e.stopPropagation){e.stopPropagation();return}e.cancelBubble=!0}function C(e){return e=="shift"||e=="ctrl"||e=="alt"||e=="meta"}function J(){if(!S){S={};for(var e in h)e>95&&e<112||h.hasOwnProperty(e)&&(S[h[e]]=e)}return S}function U(e,t,a){return a||(a=J()[e]?"keydown":"keypress"),a=="keypress"&&t.length&&(a="keydown"),a}function X(e){return e==="+"?["+"]:(e=e.replace(/\+{2}/g,"+plus"),e.split("+"))}function I(e,t){var a,c,b,M=[];for(a=X(e),b=0;b<a.length;++b)c=a[b],q[c]&&(c=q[c]),t&&t!="keypress"&&g[c]&&(c=g[c],M.push("shift")),C(c)&&M.push(c);return t=U(c,M,t),{key:c,modifiers:M,action:t}}function D(e,t){return e===null||e===d?!1:e===t?!0:D(e.parentNode,t)}function v(e){var t=this;if(e=e||d,!(t instanceof v))return new v(e);t.target=e,t._callbacks={},t._directMap={};var a={},c,b=!1,M=!1,E=!1;function K(r){r=r||{};var s=!1,l;for(l in a){if(r[l]){s=!0;continue}a[l]=0}s||(E=!1)}function j(r,s,l,i,u,m){var f,_,A=[],k=l.type;if(!t._callbacks[r])return[];for(k=="keyup"&&C(r)&&(s=[r]),f=0;f<t._callbacks[r].length;++f)if(_=t._callbacks[r][f],!(!i&&_.seq&&a[_.seq]!=_.level)&&k==_.action&&(k=="keypress"&&!l.metaKey&&!l.ctrlKey||$(s,_.modifiers))){var Q=!i&&_.combo==u,W=i&&_.seq==i&&_.level==m;(Q||W)&&t._callbacks[r].splice(f,1),A.push(_)}return A}function x(r,s,l,i){t.stopCallback(s,s.target||s.srcElement,l,i)||r(s,l)===!1&&(V(s),H(s))}t._handleKey=function(r,s,l){var i=j(r,s,l),u,m={},f=0,_=!1;for(u=0;u<i.length;++u)i[u].seq&&(f=Math.max(f,i[u].level));for(u=0;u<i.length;++u){if(i[u].seq){if(i[u].level!=f)continue;_=!0,m[i[u].seq]=1,x(i[u].callback,l,i[u].combo,i[u].seq);continue}_||x(i[u].callback,l,i[u].combo)}var A=l.type=="keypress"&&M;l.type==E&&!C(r)&&!A&&K(m),M=_&&l.type=="keydown"};function G(r){typeof r.which!="number"&&(r.which=r.keyCode);var s=T(r);if(s){if(r.type=="keyup"&&b===s){b=!1;return}t.handleKey(s,B(r),r)}}function Y(){clearTimeout(c),c=setTimeout(K,1e3)}function z(r,s,l,i){a[r]=0;function u(k){return function(){E=k,++a[r],Y()}}function m(k){x(l,k,r),i!=="keyup"&&(b=T(k)),setTimeout(K,10)}for(var f=0;f<s.length;++f){var _=f+1===s.length,A=_?m:u(i||I(s[f+1]).action);N(s[f],A,i,r,f)}}function N(r,s,l,i,u){t._directMap[r+":"+l]=s,r=r.replace(/\s+/g," ");var m=r.split(" "),f;if(m.length>1){z(r,m,s,l);return}f=I(r,l),t._callbacks[f.key]=t._callbacks[f.key]||[],j(f.key,f.modifiers,{type:f.action},i,r,u),t._callbacks[f.key][i?"unshift":"push"]({callback:s,modifiers:f.modifiers,action:f.action,seq:i,level:u,combo:r})}t._bindMultiple=function(r,s,l){for(var i=0;i<r.length;++i)N(r[i],s,l)},O(e,"keypress",G),O(e,"keydown",G),O(e,"keyup",G)}v.prototype.bind=function(e,t,a){var c=this;return e=e instanceof Array?e:[e],c._bindMultiple.call(c,e,t,a),c},v.prototype.unbind=function(e,t){var a=this;return a.bind.call(a,e,function(){},t)},v.prototype.trigger=function(e,t){var a=this;return a._directMap[e+":"+t]&&a._directMap[e+":"+t]({},e),a},v.prototype.reset=function(){var e=this;return e._callbacks={},e._directMap={},e},v.prototype.stopCallback=function(e,t){var a=this;if((" "+t.className+" ").indexOf(" mousetrap ")>-1||D(t,a.target))return!1;if("composedPath"in e&&typeof e.composedPath=="function"){var c=e.composedPath()[0];c!==e.target&&(t=c)}return t.tagName=="INPUT"||t.tagName=="SELECT"||t.tagName=="TEXTAREA"||t.isContentEditable},v.prototype.handleKey=function(){var e=this;return e._handleKey.apply(e,arguments)},v.addKeycodes=function(e){for(var t in e)e.hasOwnProperty(t)&&(h[t]=e[t]);S=null},v.init=function(){var e=v(d);for(var t in e)t.charAt(0)!=="_"&&(v[t]=function(a){return function(){return e[a].apply(e,arguments)}}(t))},v.init(),p.Mousetrap=v,typeof n<"u"&&n.exports&&(n.exports=v),typeof define=="function"&&define.amd&&define(function(){return v})})(typeof window<"u"?window:null,typeof window<"u"?document:null)}),R=oe(fe());(function(o){if(o){var n={},p=o.prototype.stopCallback;o.prototype.stopCallback=function(d,P,h,y){var g=this;return g.paused?!0:n[h]||n[y]?!1:p.call(g,d,P,h)},o.prototype.bindGlobal=function(d,P,h){var y=this;if(y.bind(d,P,h),d instanceof Array){for(var g=0;g<d.length;g++)n[d[g]]=!0;return}n[d]=!0},o.init()}})(typeof Mousetrap<"u"?Mousetrap:void 0);var le=o=>{o.directive("mousetrap",(n,{modifiers:p,expression:d},{evaluate:P})=>{let h=()=>d?P(d):n.click();p=p.map(y=>y.replace("-","+")),p.includes("global")&&(p=p.filter(y=>y!=="global"),R.default.bindGlobal(p,y=>{y.preventDefault(),h()})),R.default.bind(p,y=>{y.preventDefault(),h()})})},F=le;document.addEventListener("alpine:init",()=>{window.Alpine.plugin(F),window.Alpine.store("sidebar",{isOpen:window.Alpine.$persist(!0).as("isOpen"),collapsedGroups:window.Alpine.$persist(null).as("collapsedGroups"),groupIsCollapsed:function(n){return this.collapsedGroups.includes(n)},collapseGroup:function(n){this.collapsedGroups.includes(n)||(this.collapsedGroups=this.collapsedGroups.concat(n))},toggleCollapsedGroup:function(n){this.collapsedGroups=this.collapsedGroups.includes(n)?this.collapsedGroups.filter(p=>p!==n):this.collapsedGroups.concat(n)},close:function(){this.isOpen=!1},open:function(){this.isOpen=!0}});let o=localStorage.getItem("theme")??"system";window.Alpine.store("theme",o==="dark"||o==="system"&&window.matchMedia("(prefers-color-scheme: dark)").matches?"dark":"light"),window.addEventListener("theme-changed",n=>{let p=n.detail;localStorage.setItem("theme",p),p==="system"&&(p=window.matchMedia("(prefers-color-scheme: dark)").matches?"dark":"light"),window.Alpine.store("theme",p)}),window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change",n=>{localStorage.getItem("theme")==="system"&&window.Alpine.store("theme",n.matches?"dark":"light")}),window.Alpine.effect(()=>{window.Alpine.store("theme")==="dark"?document.documentElement.classList.add("dark"):document.documentElement.classList.remove("dark")})});})();

function getUrlAfterAdmin() {
    const url = window.location.pathname;
    const index = url.indexOf('admin/');
    if (index !== -1) {
        return url.substring(index + 'admin/'.length);
    }
    return '';
}

function calcularDesbasteTecnico(pesoNetoOrigen, distancia) {
    let porcentajeRestar = 0;

    if (distancia < 300) {
        porcentajeRestar = 1.5 + ((distancia * 0.5) / 100);
    } else {
        porcentajeRestar = Math.floor(distancia / 100) * 1 + ((distancia % 100) / 100 * 1);
    }

    return Number(pesoNetoOrigen) - ((Number(pesoNetoOrigen) * porcentajeRestar) / 100);
}
function showAlert(title, message) {
    // Crear el contenedor del modal
    const modalId = 'customAlertModal';
    const fragment = document.createDocumentFragment();

    const modalBackdrop = document.createElement('div');
    modalBackdrop.id = modalId;
    modalBackdrop.tabIndex = 999;
    modalBackdrop.style.position = 'fixed';
    modalBackdrop.style.top = '0';
    modalBackdrop.style.left = '0';
    modalBackdrop.style.width = '100%';
    modalBackdrop.style.height = '100%';
    modalBackdrop.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    modalBackdrop.style.display = 'flex';
    modalBackdrop.style.alignItems = 'center';
    modalBackdrop.style.justifyContent = 'center';


    const modalDialog = document.createElement('div');
    modalDialog.style.maxWidth = '500px';
    modalDialog.style.width = '100%';
    modalDialog.style.margin = '0 auto';

    const modalContent = document.createElement('div');
    modalContent.style.backgroundColor = '#fff';
    modalContent.style.borderRadius = '5px';
    modalContent.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
    modalContent.style.overflow = 'hidden';

    const modalHeader = document.createElement('div');
    modalHeader.style.display = 'flex';
    modalHeader.style.justifyContent = 'space-between';
    modalHeader.style.alignItems = 'center';
    modalHeader.style.padding = '15px';
    modalHeader.style.borderBottom = '1px solid #ddd';

    const modalTitle = document.createElement('h5');
    modalTitle.style.margin = '0';
    modalTitle.style.fontSize = '18px';
    modalTitle.style.fontWeight = 'bold';
    modalTitle.innerHTML = title + ' <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-1" style="width: 24px; height: 24px;display:inline-block;color:#dc3545"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" /></svg>';
    

    const closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.style.background = 'none';
    closeButton.style.border = 'none';
    closeButton.style.fontSize = '20px';
    closeButton.style.cursor = 'pointer';
    closeButton.textContent = '×';
    closeButton.setAttribute('aria-label', 'Cerrar');

    modalHeader.appendChild(modalTitle);
    modalHeader.appendChild(closeButton);

    const modalBody = document.createElement('div');
    modalBody.style.padding = '15px';

    const modalMessage = document.createElement('p');
    modalMessage.style.margin = '0';
    modalMessage.textContent = message;

    modalBody.appendChild(modalMessage);

    const modalFooter = document.createElement('div');
    modalFooter.style.display = 'flex';
    modalFooter.style.justifyContent = 'flex-end';
    modalFooter.style.padding = '15px';
    modalFooter.style.borderTop = '1px solid #ddd';

    const okButton = document.createElement('button');
    okButton.type = 'button';
    okButton.setAttribute('id','okButtonAlert');
    okButton.style.backgroundColor = '#dc3545';
    okButton.style.color = '#fff'; // Bootstrap danger color
    okButton.style.border = 'none';
    okButton.style.padding = '10px 20px';
    okButton.style.borderRadius = '5px';
    okButton.style.cursor = 'pointer';
    okButton.textContent = 'OK';

    modalFooter.appendChild(okButton);

    modalContent.appendChild(modalHeader);
    modalContent.appendChild(modalBody);
    modalContent.appendChild(modalFooter);

    modalDialog.appendChild(modalContent);
    modalBackdrop.appendChild(modalDialog);

    fragment.appendChild(modalBackdrop);

    // Insertar el modal en el DOM

    document.getElementsByTagName('body')[0].appendChild(fragment)
    // Inicializar y mostrar el modal
    // const modalElement = document.getElementById(modalId);

    okButton.addEventListener('click', () => {

        setTimeout(() => {
            document.getElementById(modalId).remove();
        }, 100);
    });
      
}

const mermaManipuleo = {
    "Maiz": 0.25,
    "Sorgo": 0.25,   
    "Trigo": 0.10,
    "Cebada": 0.20,
    "Avena": 0.20,
    "Soja": 0.25,
    "Girasol": 0.20,
    "Centeno": 0.20,
    "Triticale": 0.5,
    "Arroz": 0.13,
    "Mijo": 0.25,
}

let getMermaHumedad = () => {

    let cereal = document.getElementById('cereal').value;

    let humedad = Number(document.getElementById('humedad').value)

    let mermaOlor = document.getElementById('mermaOlor').value; 

    let mermaTierra = document.getElementById('mermaTierra').value; 

    let mermaMaterias = document.getElementById('mermaMaterias').value;
    mermaMaterias = (mermaMaterias > 1.5) ? mermaMaterias - 1.5 : 0

    if(humedad > 14.5){

        fetch('/merma-humedad', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                cereal: cereal,
                humedad: humedad
            })
        })
        .then(response => response.json())
        .then(data => {

            if (data.merma !== undefined) {

                let merma = Number(data.merma) + mermaManipuleo[cereal] + Number(mermaOlor) + Number(mermaMaterias) + Number(mermaTierra);
    
                document.getElementById('mermaHumedad').value = data.merma;
    
                document.getElementById('mermaManipuleo').value = mermaManipuleo[cereal];
    
                let pesoNeto = Number(document.getElementById('pesoNeto').value);
    
                let resultado = (pesoNeto - (pesoNeto * (merma / 100)));
    
                document.getElementById('pesoNetoHumedad').value = resultado;

            } else {
                console.error('No se encontró el valor de merma.');
            }
        })
        .catch(error => {
            console.error('Error al consultar la base de datos:', error);
        });

    } else {

        let merma =  Number(mermaOlor) + Number(mermaMaterias) + Number(mermaTierra);

        document.getElementById('mermaHumedad').value = 0;
    
        document.getElementById('mermaManipuleo').value = 0;
        
        let pesoNeto = Number(document.getElementById('pesoNeto').value);

        let resultado = (pesoNeto - (pesoNeto * (merma / 100)));
        console.log(pesoNeto,merma)
        document.getElementById('pesoNetoHumedad').value = resultado;


    }

}
///*********************     
//                          INDEX        
//                                   ************************/

if(getUrlAfterAdmin() === '') {

    setTimeout(() => {

        let btnBarlovento = document.getElementById('btnBarlovento')
        let btnPaihuen = document.getElementById('btnPaihuen')

        btnBarlovento.addEventListener('click', function() {

            btnBarlovento.style.display = 'none';
            btnPaihuen.style.display = 'none';

            let btnsBarlovento = document.getElementById('btnsBarlovento');

            btnsBarlovento.style.display = 'block';
        })

    }, 2500);
}

///*********************     
//                          CREATE EDIT INGRESOS       
//                                   ************************/

if(getUrlAfterAdmin() === 'barlovento-ingresos/create' || (getUrlAfterAdmin().split('/')[2] === 'edit' && (getUrlAfterAdmin().split('/')[0] === 'barlovento-ingresos'))) {

    let origen_terneros = document.getElementById('origen_terneros')
    let origen_terneras = document.getElementById('origen_terneras')
    
    origen_terneros.addEventListener('change', function() {
            let resultado = Number(origen_terneros.value) + Number(origen_terneras.value);
            document.getElementById('cantidadTotal').value = resultado;

            resultado = Number(origen_pesoNeto.value) / (Number(origen_terneros.value) + Number(origen_terneras.value));
            document.getElementById('promedio').value = resultado.toFixed(2);
    })

    origen_terneras.addEventListener('change', function() {
        let resultado = Number(origen_terneros.value) + Number(origen_terneras.value);
        document.getElementById('cantidadTotal').value = resultado;

        resultado = Number(origen_pesoNeto.value) / (Number(origen_terneros.value) + Number(origen_terneras.value));
        document.getElementById('promedio').value = resultado.toFixed(2);
    })

    let origen_pesoBruto = document.getElementById('origen_pesoBruto')
    let origen_pesoNeto = document.getElementById('origen_pesoNeto')
    let origen_desbaste = document.getElementById('origen_desbaste')

    origen_pesoNeto.addEventListener('change', function() {

        let resultado = Number(origen_pesoNeto.value) / (Number(origen_terneros.value) + Number(origen_terneras.value));
        document.getElementById('promedio').value = resultado.toFixed(2);

        if(origen_desbaste.value != '') {
            
            let resultado = (Number(origen_pesoNeto.value) - (Number(origen_pesoNeto.value) * (Number(origen_desbaste.value) / 100)));

            document.getElementById('pesoDesbaste').value = resultado;
        }

        if(origen_distancia.value != ''){

            
            let distancia = Number(origen_distancia.value);

            let nuevoPesoNeto = calcularDesbasteTecnico(Number(origen_pesoNeto.value), distancia);

            document.getElementById('pesoDesbasteTecnico').value = nuevoPesoNeto.toFixed(2)
        }


    })

    origen_desbaste.addEventListener('change', function() {

        if(origen_desbaste.value != '') {

            let resultado = (Number(origen_pesoNeto.value) - (Number(origen_pesoNeto.value) * (Number(origen_desbaste.value) / 100)));

            document.getElementById('pesoDesbaste').value = resultado;
        }

    })

    origen_distancia.addEventListener('change',function(){

        let distancia = Number(origen_distancia.value);

        let nuevoPesoNeto = calcularDesbasteTecnico(Number(origen_pesoNeto.value), distancia);

        document.getElementById('pesoDesbasteTecnico').value = nuevoPesoNeto.toFixed(2)
            
    })

    let destino_terneros = document.getElementById('destino_terneros')
    let destino_terneras = document.getElementById('destino_terneras')

    destino_terneros.addEventListener('change', function() {
        let resultado = Number(destino_terneros.value) + Number(destino_terneras.value);
        document.getElementById('cantidadTotalDestino').value = resultado;

        let pesoNeto = Number(destino_pesoBruto.value) - Number(destino_tara.value);
        document.getElementById('destino_pesoNeto').value = pesoNeto;

        let promedio = pesoNeto / (Number(destino_terneros.value) + Number(destino_terneras.value));
        document.getElementById('destino_promedio').value = promedio.toFixed(2);
    })

    destino_terneras.addEventListener('change', function() {
        let resultado = Number(destino_terneros.value) + Number(destino_terneras.value);
        document.getElementById('cantidadTotalDestino').value = resultado;

        let pesoNeto = Number(destino_pesoBruto.value) - Number(destino_tara.value);
        document.getElementById('destino_pesoNeto').value = pesoNeto;

        let promedio = pesoNeto / (Number(destino_terneros.value) + Number(destino_terneras.value));
        document.getElementById('destino_promedio').value = promedio.toFixed(2);
    })

    let destino_pesoBruto = document.getElementById('destino_pesoBruto')
    let destino_tara = document.getElementById('destino_tara')

    destino_pesoBruto.addEventListener('change', function() {

            let pesoNeto = Number(destino_pesoBruto.value) - Number(destino_tara.value);
            document.getElementById('destino_pesoNeto').value = pesoNeto;

            let promedio = pesoNeto / (Number(destino_terneros.value) + Number(destino_terneras.value));
            document.getElementById('destino_promedio').value = promedio.toFixed(2);

            let diferencia = Number(origen_pesoNeto.value) - Number(pesoNeto);

            document.getElementById('destino_diferencia').value = diferencia;

            if (Math.abs(Number(origen_pesoBruto.value) - Number(destino_pesoBruto.value)) > (Number(origen_pesoBruto.value) * 0.04)) {

                showAlert('Atención', 'El peso bruto de origen y destino difieren en más del 4%')

            }

            let desbasteTecnico = calcularDesbasteTecnico(Number(origen_pesoNeto.value), Number(origen_distancia.value));

            if(desbasteTecnico > pesoNeto) {
              
                showAlert('Atención', `El peso neto con Desbaste Tecnico es MAYOR al Peso Neto destino - Peso Neto con Desbaste Tecnico: ${desbasteTecnico.toLocaleString('de-DE')} kg / Peso Neto Destino: ${pesoNeto.toLocaleString('de-DE')} kg`)
            }

            

    })

    destino_tara.addEventListener('change', function() {

        let pesoNeto = Number(destino_pesoBruto.value) - Number(destino_tara.value);
        document.getElementById('destino_pesoNeto').value = pesoNeto;

        let promedio = pesoNeto / (Number(destino_terneros.value) + Number(destino_terneras.value));
        document.getElementById('destino_promedio').value = promedio.toFixed(2);

        let diferencia = Number(origen_pesoNeto.value) - Number(pesoNeto);

        document.getElementById('destino_diferencia').value = diferencia;

        let desbasteTecnico = calcularDesbasteTecnico(Number(origen_pesoNeto.value), Number(origen_distancia.value));

        if(desbasteTecnico > pesoNeto) {
                showAlert('Atención', `El peso neto con Desbaste Tecnico es MAYOR al Peso Neto destino - Peso Neto con Desbaste Tecnico: ${desbasteTecnico.toLocaleString('de-DE')} kg / Peso Neto Destino: ${pesoNeto.toLocaleString('de-DE')} kg`)
        }

    })
    
    if(getUrlAfterAdmin().split('/')[2] === 'edit' && (getUrlAfterAdmin().split('/')[0] === 'barlovento-ingresos')) {
    
        setTimeout(() => {
            let resultado = Number(origen_terneros.value) + Number(origen_terneras.value);
            document.getElementById('cantidadTotal').value = resultado;

            resultado = Number(origen_pesoNeto.value) / (Number(origen_terneros.value) + Number(origen_terneras.value));
            document.getElementById('promedio').value = resultado.toFixed(2);

            resultado = (Number(origen_pesoNeto.value) - (Number(origen_pesoNeto.value) * (Number(origen_desbaste.value) / 100)));
            document.getElementById('pesoDesbaste').value = resultado;

            resultado = Number(destino_terneros.value) + Number(destino_terneras.value);
            document.getElementById('cantidadTotalDestino').value = resultado;
            
            resultado = Number(destino_pesoBruto.value) - Number(destino_tara.value);
            document.getElementById('destino_diferencia').value = resultado;

            let pesoNeto = Number(destino_pesoBruto.value) - Number(destino_tara.value);
                document.getElementById('destino_pesoNeto').value = pesoNeto;

            let promedio = pesoNeto / (Number(destino_terneros.value) + Number(destino_terneras.value));
            document.getElementById('destino_promedio').value = promedio.toFixed(2);

            let diferencia = Number(origen_pesoNeto.value) - Number(pesoNeto);

            document.getElementById('destino_diferencia').value = diferencia;

            let distancia = Number(origen_distancia.value);

            let nuevoPesoNeto = calcularDesbasteTecnico(Number(origen_pesoNeto.value), distancia);
            document.getElementById('pesoDesbasteTecnico').value = nuevoPesoNeto.toFixed(2)

            //CUANDO CARGA EDITAR INGRESO

            if(nuevoPesoNeto > pesoNeto) {
                showAlert('Atención', `El peso neto con Desbaste Tecnico es MAYOR al Peso Neto destino - Peso Neto con Desbaste Tecnico: ${nuevoPesoNeto.toLocaleString('de-DE')} kg / Peso Neto Destino: ${pesoNeto.toLocaleString('de-DE')} kg`)
            }



        }, 2000);
    }

}

///*********************     
//                          CREATE EDIT EGRESOS       
//                                   ************************/

if(getUrlAfterAdmin() === 'barlovento-egresos/create' || (getUrlAfterAdmin().split('/')[2] === 'edit' && (getUrlAfterAdmin().split('/')[0] === 'barlovento-egresos'))) {

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

        let resultadoDesbaste = resultado - ((resultado * 8) / 100);

        document.getElementById('pesoNetoDesbastado').value = resultadoDesbaste;

    })

    if(getUrlAfterAdmin().split('/')[2] === 'edit' && (getUrlAfterAdmin().split('/')[0] === 'barlovento-egresos')) {
    
        setTimeout(() => {
        let resultado = Number(novillos.value) + Number(vaquillonas.value);
        document.getElementById('cantidad').value = resultado;

        resultado = Number(pesoBruto.value) - Number(tara.value);
        document.getElementById('pesoNeto').value = resultado;

        let resultadoDesbaste = resultado - ((resultado * 8) / 100);

        document.getElementById('pesoNetoDesbastado').value = resultadoDesbaste;
       
        }, 1000);
    }

}

///*********************     
//                          CREATE CEREALES       
//                                   ************************/

if(getUrlAfterAdmin() === 'barlovento-cereales/create' || getUrlAfterAdmin() === 'paihuen-cereales/create' || getUrlAfterAdmin() === 'paihuen-egreso-cereales/create') {

    let pesoBruto = document.getElementById('pesoBruto')
    let tara = document.getElementById('pesoTara')

    pesoBruto.addEventListener('change', function() {
        let resultado = Number(pesoBruto.value) - Number(tara.value);
        document.getElementById('pesoNeto').value = resultado;

        if(getUrlAfterAdmin() === 'barlovento-cereales/create' || getUrlAfterAdmin() === 'paihuen-cereales/create')
            getMermaHumedad()


    })

    tara.addEventListener('change', function() {

        let resultado = Number(pesoBruto.value) - Number(tara.value);
        document.getElementById('pesoNeto').value = resultado;

        if(getUrlAfterAdmin() === 'barlovento-cereales/create' || getUrlAfterAdmin() === 'paihuen-cereales/create')
            getMermaHumedad()


    })

    if(getUrlAfterAdmin() === 'barlovento-cereales/create' || getUrlAfterAdmin() === 'paihuen-cereales/create') {

        const fields = ['humedad', 'mermaMaterias', 'mermaTierra', 'mermaOlor'];
        fields.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', getMermaHumedad);
            }
        });
    }
}


///*********************     
//                          EDIT CEREALES       
//                                   ************************/

if(getUrlAfterAdmin().split('/')[2] === 'edit' && (getUrlAfterAdmin().split('/')[0] === 'barlovento-cereales' || getUrlAfterAdmin().split('/')[0] === 'paihuen-cereales' ||  getUrlAfterAdmin().split('/')[0]    === 'paihuen-egreso-cereales')) {
    
    let pesoBruto = document.getElementById('pesoBruto')
    let tara = document.getElementById('pesoTara')

    setTimeout(() => {

        let resultado = Number(pesoBruto.value) - Number(tara.value);

        document.getElementById('pesoNeto').value = resultado;

        // if(getUrlAfterAdmin() === 'barlovento-cereales/create' || getUrlAfterAdmin() === 'paihuen-cereales/create')
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

    if(getUrlAfterAdmin() === 'barlovento-cereales/create' || getUrlAfterAdmin() === 'paihuen-cereales/create'){
        
        let humedad = document.getElementById('humedad')

        humedad.addEventListener('change', function() {
            
                getMermaHumedad()

        })

        if(getUrlAfterAdmin().split('/')[2] === 'edit' && (getUrlAfterAdmin().split('/')[0] === 'paihuen-cereales')) {

            setTimeout(() => {

                let resultado = Number(pesoBruto.value) - Number(tara.value);
                document.getElementById('pesoNeto').value = resultado;
            
            }, 1000);
            
        }
    }

}




