<?

/**
 * Выводит переданное значение на экран (улучшенный аналог var_dump)
 * @param mixed $data Значение для просмотра
 * @param string $name Название
 */
function debug($data, $name = NULL)
{
    static $nestingLevel = 0;
    static $rezult = '';

    if ($nestingLevel == 0) {
        $rezult .= str_repeat('    ', $nestingLevel) . '<span class="' . (is_array($data) ? 'array' : (is_object($data) ? 'object' : 'var')) . '"><span class="key name' . (is_array($data) ? ' arr_key' : (is_object($data) ? ' obj_key' : '')) . '">' . (isset($name) ? $name : 'debug') . ' <span class="arrow">&#10148;</span> </span>';
    }

    if (is_array($data) || is_object($data)) {
        if (is_array($data)) {
            $rezult .= '<span class="type">(Array ' . count($data) . ')</span>' . PHP_EOL . str_repeat('    ', $nestingLevel) . '<span class="value">(';
        } else {
            $rezult .= '<span class="type">' . get_class($data) . ' (Object)</span>' . PHP_EOL . str_repeat('    ', $nestingLevel) . '<span class="value">(';
        }
        $nestingLevel++;
        foreach ($data as $key => $value) {
            $rezult .= PHP_EOL . str_repeat('    ', $nestingLevel) . '<span class="' . (is_array($value) ? 'array' : (is_object($value) ? 'object' : 'var')) . '"><span class="key' . (is_array($value) ? ' arr_key' : (is_object($value) ? ' obj_key' : '')) . '">[' . $key . '] <span class="arrow">&#10148;</span> </span>';
            debug($value);
            $rezult .= '</span>';
        }
        $nestingLevel--;
        $rezult .= PHP_EOL . str_repeat('    ', $nestingLevel) . ')</span>';
    } else {
        if (isset($data)) {
            switch (gettype($data)) {
                case 'integer':
                    $rezult .= '<span class="value int">' . $data . '</span> <span class="type">(int ' . strlen((string)$data) . ')</span>';
                    break;
                case 'string':
                    $rezult .= '<span class="value str">' . $data . '</span> <span class="type">(str ' . strlen($data) . ')</span>';
                    break;
                case 'boolean':
                    $rezult .= '<span class="value bool">' . ($data == true ? 'true' : 'false') . '</span> <span class="type">(bool)</span>';
                    break;
                default:
                    $rezult .= 'todo';
            }
        } else {
            $rezult .= '<span class="type">(NULL)</span>';
        }
    }

    if ($nestingLevel <= 0) {
        echo '<pre class="debug">' . $rezult . '</pre>';
        $nestingLevel = 0;
        $rezult = '';
    }
}
?>
<script>
    /**
     * Создание метода, аналога querySelector, только с поиском вверх по родителям DOM дерева
     * @param {string} selector 
     * @returns {Node}
     */
    Element.prototype.querySelectorParent = function(selector) {
        let element = this
        if (element === null) return null
        while (true) {
            if (element.matches(selector)) return element
            if (element.parentElement === null) return null
            element = element.parentElement
        }
    }

    document.addEventListener('DOMContentLoaded', function() {

        const debug = document.querySelectorAll('pre.debug')
        debug?.forEach(debugElement => {

            debugElement.setAttribute('style', 'font-size:14px;padding:20px;margin:0;display:block;font-family:Consolas,monospace;color:#368bd2;line-height:1.4em;background-color:#303030;border:3px solid #368bd2;border-radius:20px;')
            const styles = {
                'int': 'color:#8bd465;',
                'str': 'color:#c28972;',
                'bool': 'color:#ff5b5b;',
                'type': 'color:#fafab9;',
                'arrow': 'color:#368bd2;',
                'obj_key': 'cursor:pointer;',
                'arr_key': 'cursor:pointer;',
                'name': 'color:#0d9418;',
            }
            for (let key in styles) {
                debugElement.querySelectorAll(`.${key}`)?.forEach(el => el.setAttribute('style', styles[key]))
            }

            debugElement.addEventListener('click', event => {
                let eventTarget = null
                if ((eventTarget = event.target.querySelectorParent('.arr_key')) || (eventTarget = event.target.querySelectorParent('.obj_key'))) {
                    const body = eventTarget.parentElement.querySelector('.value')
                    body.hidden = !body.hidden

                    const arrow = eventTarget.querySelector('.arrow')
                    if (eventTarget.parentElement.querySelector('.value').hidden == true) {
                        arrow.innerHTML = '&#11167;'
                        arrow.style.color = '#ff5b5b'
                    } else {
                        arrow.innerHTML = '&#10148;'
                        arrow.style.color = '#368bd2'
                    }
                }
            })
        })

    })
</script>