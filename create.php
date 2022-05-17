<?php include dirname(__FILE__).DIRECTORY_SEPARATOR.'includes/headers.php'; ?>

<div style="box-sizing: border-box; padding: 5%;">
    <h3 class="ui center aligned header">Creacion de evento</h3>
    <div class="ui two column doubling grid">
        <div class="column">
            <h3>Options</h3>
            <div class="ui styled fluid accordion">
                <div class="title" onclick="openAccordionChangeColor(event,'data')">
                    <i class="dropdown icon"></i>
                    <i class="circular orange info icon"></i>            
                    Datos
                </div>

                <div class="content" data-step="data">
                    <div class="ui corner labeled input">
                        <input type="text" placeholder="Nombre">
                        <div class="ui corner orange label">
                            <i class="asterisk icon"></i>
                        </div>
                    </div>
                    <div class="ui corner labeled input">
                        <input type="date" value="<?= date('Y-m-d') ?>">
                        <div class="ui corner orange label">
                            <i class="asterisk icon"></i>
                        </div>
                    </div>
                    <div class="ui corner labeled input">
                        <input type="text" placeholder="Localización">
                        <div class="ui corner label orange">
                            <i class="asterisk icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ui styled fluid accordion" >
                <div class="title" onclick="openAccordionChangeColor(event,'route')">
                    <i class="dropdown icon"></i>
                    <i class="teal circular truck icon"></i>            
                    Ruta
                </div>

                <div class="content" data-step="route">
                    <button class="ui primary button" type="submit">
                        Enviar
                    </button>
                </div>
            </div>
            <div class="ui styled fluid accordion" >
                <div class="title" onclick="openAccordionChangeColor(event,'map')">
                    <i class="dropdown icon"></i>
                    <i class="teal circular map icon"></i>            
                    Opciones de Mapa
                </div>

                <div class="content" data-step="map">
                    <button class="ui primary button" type="submit">
                        Enviar
                    </button>
                </div>
            </div>
        </div>
        <div class="column" style="position: sticky; top: 50px; left: 50px;">
            <h3>Map</h3>
            <div class="ui segment">Content</div>
        </div>
    </div>
</div>

<script>
    function openAccordionChangeColor(ev, step) {
        const allIcons = [...document.querySelectorAll('div.ui.styled.fluid.accordion .title .circular')];
        const allContents = [...document.querySelectorAll('div.ui.styled.fluid.accordion .content.active')];

        allIcons.forEach(icon => {
            icon.classList.remove('orange');
            icon.classList.add('teal');
        });
        
        allContents.filter(it => it.dataset.step !== step).forEach(content => {
            content.classList.remove('active');
        });

        const current = ev.target.children[1];
        const currentContent = ev.target.nextElementSibling;
        // console.log(currentContent);
        current.classList.remove('teal');
        current.classList.add('orange');
    }
</script>

<?php include dirname(__FILE__).DIRECTORY_SEPARATOR.'includes/footer.php'; ?>