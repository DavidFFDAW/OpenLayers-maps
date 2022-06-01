class UsedColors {
    constructor(usedColorsDivId) {
        this.div = document.getElementById(usedColorsDivId);
        this.colors = [];
        this.changeColorCallback = _ => false;
    }

    setChangeColorCallback(callback) { 
        this.changeColorCallback = callback;
    }

    reprintDiv() { 
        const coloreds = this.colors.map(color => {
            const colololo = document.createElement('div');
            colololo.classList.add('colololo');

            const div = document.createElement('div');
            div.classList.add('color');
            div.style.backgroundColor = color;
            div.onclick = () => this.changeColorCallback(color);

            const ceferino = document.createElement('span');
            ceferino.classList.add('ceferino');
            ceferino.textContent = color;

            colololo.appendChild(div);
            colololo.appendChild(ceferino);

            return colololo;
        });

        for (const colored of coloreds) { 
            this.div.appendChild(colored);
        }
    }

    add(color) { 
        if (!this.colors.includes(color)) {
            this.colors.push(color);
            this.reprintDiv();
        }
    }
}