<?

/**
 * Выводит переданное значение на экран (улучшенный аналог var_dump)
 * @param mixed $data Значение для просмотра
 * @param string $name Название
 */
function debug($data, $name = NULL)
{
    $style = [
        'debug' => 'font-size:14px;padding:20px;margin:0;display:block;font-family:Consolas,monospace;color:#368bd2;line-height:1.4em;background-color:#303030;border:' . ($name == "ERROR" ? '5px solid #ff3939' : '3px solid #368bd2') . ';border-radius:20px;',
        'int' => 'color:#8bd465;',
        'str' => 'color:#c28972;',
        'bool' => 'color:#ff5b5b;',
        'type' => 'color:#fafab9;',
        'arrow' => 'color:#368bd2;',
        'arr_obj_key' => 'cursor:pointer;',
        'name' => 'color:' . ($name == "ERROR" ? '#ff5b5b' : '#0d9418') . ';',
    ];

    static $nestingLevel = 0;
    static $rezult = '';

    if ($nestingLevel == 0) {
        $rezult .= '
        <div class="' . (is_array($data) ? 'array' : (is_object($data) ? 'object' : 'var')) . '">
            <span class="key name' . (is_array($data) || is_object($data) ? ' arr_obj_key' : '') . '" style="' . (is_array($data) || is_object($data) ? $style['arr_obj_key'] : '') . '' . $style['name'] . '">
                ' . str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $nestingLevel)  . (isset($name) ? $name : 'debug') . '
                <span class="arrow">&#10148;</span>
            </span>';
    }

    if (is_array($data) || is_object($data)) {
        $rezult .= '
        <span class="type" style="' . $style['type'] . '">' . (is_array($data) ? '(Array ' . count($data) . ')' : get_class($data) . ' (Object)') . '</span>
        <div class="value">' . str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $nestingLevel) . '(';
        $nestingLevel++;
        foreach ($data as $key => $value) {
            $rezult .= '
            <div class="' . (is_array($value) ? 'array' : (is_object($value) ? 'object' : 'var')) . '">
                <span class="key' . (is_array($value) || is_object($value) ? ' arr_obj_key' : '') . '" style="' . (is_array($value) || is_object($value) ? $style['arr_obj_key'] : '') . '">
                    ' . str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $nestingLevel) . '[' . $key . ']
                    <span class="arrow" style="' . $style['arrow'] . '">&#10148;</span>
                </span>';
            debug($value);
            $rezult .= '</div>';
        }

        if (is_object($data)) {
            $methods = get_class_methods($data);
            if (!empty($methods)) {
                $rezult .= '
                    <div class="methods">
                        <span class="key arr_obj_key" style="' .  $style['arr_obj_key'] . '">' . str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $nestingLevel) . '[methods]
                            <span class="arrow" style="' . $style['arrow'] . '">&#10148;</span>
                        </span>
                        <span class="type" style="' . $style['type'] . '"> (public methods) </span>
                        <div class="value">' . str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $nestingLevel) . '(';
                $nestingLevel++;
                foreach ($methods as $i => $method) {
                    $rezult .= '
                            <div class="var">
                                <span class="key">' . str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $nestingLevel) . '[' . $i . ']
                                    <span class="arrow" style="' . $style['arrow'] . '">&#10148;</span>
                                </span>
                                <span class="value" style="' . $style['str'] . '">' . $method . '()</span> <span class="type" style="' . $style['type'] . '">(method)</span>
                            </div>';
                }
                $nestingLevel--;
                $rezult .=  str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $nestingLevel) . ')</div>';
                $rezult .= '</div>';
            }
        }

        $nestingLevel--;
        $rezult .= str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $nestingLevel) . ')</div>';
    } else {
        if (isset($data)) {
            switch (gettype($data)) {
                case 'integer':
                    $rezult .= '<span class="value" style="' . $style['int'] . '">' . $data . '</span> <span class="type" style="' . $style['type'] . '">(int ' . strlen((string)$data) . ')</span>';
                    break;
                case 'string':
                    $rezult .= '<span class="value" style="' . $style['str'] . '">' . $data . '</span> <span class="type" style="' . $style['type'] . '">(str ' . strlen($data) . ')</span>';
                    break;
                case 'boolean':
                    $rezult .= '<span class="value" style="' . $style['bool'] . '">' . ($data == true ? 'true' : 'false') . '</span> <span class="type" style="' . $style['type'] . '">(bool)</span>';
                    break;
                default:
                    $rezult .= 'todo';
            }
        } else {
            $rezult .= '<span class="type" style="' . $style['type'] . '">(NULL)</span>';
        }
    }

    if ($nestingLevel <= 0) {
        $rezult .= '</div>';
        echo '<div class="debug" style="' . $style['debug'] . '">' . $rezult . '</div>
        <script>
            Element.prototype.querySelectorParent = function(selector) {
                let element = this
                if (element === null) return null
                while (true) {
                    if (element.matches(selector)) return element
                    if (element.parentElement === null) return null
                    element = element.parentElement
                }
            }
            document.addEventListener(\'DOMContentLoaded\', function() {
                if(document.debug == null){
                    document.debug = document.querySelectorAll(\'div.debug\')
                    document.debug?.forEach(debugElement => {
                        debugElement.addEventListener(\'click\', event => {
                            let eventTarget = null
                            if (eventTarget = event.target.querySelectorParent(\'.arr_obj_key\')) {
                                const body = eventTarget.parentElement.querySelector(\'.value\')
                                body.hidden = !body.hidden

                                const arrow = eventTarget.querySelector(\'.arrow\')
                                if (eventTarget.parentElement.querySelector(\'.value\').hidden == true) {
                                    arrow.innerHTML = \'&#11167;\'
                                    arrow.style.color = \'#ff5b5b\'
                                } else {
                                    arrow.innerHTML = \'&#10148;\'
                                    arrow.style.color = \'#368bd2\'
                                }
                            }
                        })
                    })
                }
            })
        </script>';
        $nestingLevel = 0;
        $rezult = '';
    }
}
