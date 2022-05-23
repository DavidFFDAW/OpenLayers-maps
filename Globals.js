class Globals { 
    constructor() {
        this.globals = {
            'routing': true,
        };
    }
    
    get(name) {
        return this.globals[name];
    }
    
    set(name, value) {
        console.log(this.globals);
        this.globals = Object.entries(this.globals).reduce((acc, [key, _]) => ({
            ...acc,
            [key]: false,
        }), {});
        console.log(this.globals);

        this.globals[name] = value;
    }
}