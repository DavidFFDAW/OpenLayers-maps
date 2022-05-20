const word = 'awqeqwqwassseqw';

const countLetters = word => {
    return word.split('').reduce((acc, current) => {
        acc[current] = acc[current] ? acc[current] + 1 : 1;
        return acc;
    }, {});

};

const po = countLetters(word);
console.log(po);

const maximum = Object.entries(po).find(([_, value]) => {
    return value === Math.max(...Object.values(po));
});

console.log("caracter: `"+maximum[0]+"` repetido"+" "+maximum[1]+" veces");