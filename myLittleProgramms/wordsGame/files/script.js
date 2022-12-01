document.addEventListener('DOMContentLoaded', () => {

    document.addEventListener('click', (event) => {
        if (event.target.classList.contains('switchChar')) {
            event.target.parentElement.querySelector('input').classList.toggle('unCurrentChar')
            event.target.classList.toggle('unCurrentChar')

            showCurrentWords()
        }
    })

    document.addEventListener('input', (event) => {
        showCurrentWords()
    })



    function showCurrentWords() {
        const countShowWords = 100

        const currentCharsPosition = document.querySelectorAll('.currentCharsPosition .chars input')
        const currentChars = [];
        for (let i = 0; i < currentCharsPosition.length; i++) {
            currentChars.push(!currentCharsPosition[i].classList.contains('unCurrentChar') ? currentCharsPosition[i].value.toLowerCase() : '')
        }

        const unCurrentCharsPosition = document.querySelectorAll('.currentCharsPosition .chars input')
        const unCurrentChars = [];
        for (let i = 0; i < unCurrentCharsPosition.length; i++) {
            unCurrentChars.push(unCurrentCharsPosition[i].classList.contains('unCurrentChar') ? unCurrentCharsPosition[i].value.toLowerCase() : '')
        }

        const haveChars = document.querySelector('.haveChars input').value.toLowerCase().split('')
        const notHaveErrorChars = document.querySelector('.notHaveErrorChars input').value.toLowerCase().split('')
        const words = document.querySelector('.words-container .words').dataset.allwords.split(',')

        const currentWords = words.filter(word => {
            const charsWord = word.split('')

            for (let i = 0; i < currentChars.length; i++)
                if (currentChars[i] !== '' && currentChars[i] !== charsWord[i])
                    return false

            for (let i = 0; i < unCurrentChars.length; i++)
                if (unCurrentChars[i] !== '' && unCurrentChars[i] === charsWord[i])
                    return false

            for (let i = 0; i < haveChars.length; i++)
                if (!word.includes(haveChars[i]))
                    return false

            for (let i = 0; i < notHaveErrorChars.length; i++)
                if (word.includes(notHaveErrorChars[i]))
                    return false

            return true
        })

        document.querySelector('.words-container .words').innerHTML = `<span class="countWordsResult">` + currentWords.length + `</span>`
        currentWords.forEach((word, index) => {
            if (index >= countShowWords) return ''
            document.querySelector('.words-container .words').insertAdjacentHTML('beforeend', `<a href="https://www.google.com/search?q=${word}" target="_blank">` + word + `</a><br>`)
        })
    }
})