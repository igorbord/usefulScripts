
/**
 * Создание метода, аналога querySelector, только с поиском вверх по родителям DOM дерева
 * @param {string} selector 
 * @returns {Node}
 */
Element.prototype.querySelectorParent = function (selector) {
    let element = this
    if (element === null) return null
    while (true) {
        if (element.matches(selector)) return element
        if (element.parentElement === null) return null
        element = element.parentElement
    }
}