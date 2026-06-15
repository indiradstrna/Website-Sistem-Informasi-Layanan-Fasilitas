const fs = require('fs');
const js = fs.readFileSync('script3.js', 'utf8');

const clean = js
  .replace(/\/\*[\s\S]*?\*\//g, m => ' '.repeat(m.length))
  .replace(/\/\/.*/g, m => ' '.repeat(m.length))
  .replace(/'(?:\\'|[^'])*'/g, m => `'` + ' '.repeat(m.length-2) + `'`)
  .replace(/"(?:\\"|[^"])*"/g, m => `"` + ' '.repeat(m.length-2) + `"`)
  .replace(/`(?:\\`|[^`])*`/g, m => '`' + ' '.repeat(m.length-2) + '`');

let stack = [];
for (let i = 0; i < clean.length; i++) {
  if (clean[i] === '{') stack.push(i);
  if (clean[i] === '}') {
    if (stack.length > 0) stack.pop();
    else console.log("EXTRA closing brace at index", i);
  }
}
console.log("Unclosed opening braces:");
for (let i of stack) {
  console.log("Index:", i, "=>", clean.substring(Math.max(0, i-50), i+50).trim());
}
