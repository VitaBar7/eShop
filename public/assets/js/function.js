
const handleAddToCart = (reference_id) => {
    const divColors = document.getElementById('display-colors');
        document.getElementById('js-select-size').addEventListener('change', e => {
            divColors.innerHTML = '';
            const size = e.target.value;
            axios.post('/api/reference/size/colors', { reference_id : reference_id, size_id : size })
            .then(response => response.data)
            .then(data => {

                const select = document.createElement('select');
                select.classList.add("form-select");
                select.name = 'color';
                select.id = "js-select-color"
                
                const defaultOpt = document.createElement('option');
                defaultOpt.innerHTML = 'See colors in this size';
                
                select.appendChild(defaultOpt);
                divColors.appendChild(select);

                const colors = Object.values(data.colors);
                const idColors = Object.keys(data.colors);
                console.log(colors, idColors);

                for (let i = 0; i < colors.length; i++) {
                    const option = document.createElement('option');
                    option.value = idColors[i];
                    option.innerHTML = colors[i];
                    select.appendChild(option);
                }

                document.getElementById('js-select-color').addEventListener('change', e => {
                    console.log(e.target.value);
                    axios.post('/api/reference/article-qty'), {reference_id: reference_id, size_id : size, color_id: e.target.value}
                    .then(response=>response.data)
                    .then(data => {
                        document.getElementsById('display-stock').innerText = `Stock disponible: ${data.article_qty}`//completer
                    });
                    //new request to get the article and the colors, sizes available
                    document.getElementById('js-add-to-cart').classList.remove('d-none');
                })
            })
        });

}