<?php 

 return [
    "date_format" => ":attribute no coincide con el formato :format.",
    "active_url" => ":attribute no es una URL válida.",
    "date" => ":attribute no es una fecha válida.",
    "in" => ":attribute no es válido.",
    "exists" => ":attribute no es válido.",
    "not_in" => ":attribute no es válido.",
    "alpha_num" => ":attribute solo puede contener letras y números.",
    "alpha" => ":attribute solo puede contener letras.",
    "dimensions" => ":attribute tiene dimensiones de imágen no válidas.",
    "accepted" => ":attribute tiene que aceptarse.",
    "digits" => ":attribute tiene que contener :digits dígitos.",
    "digits_between" => ":attribute tiene que contener entre :min y :max dígitos.",
    "alpha_dash" => ":attribute tiene que contener letras, números, guiones y guiones bajos.",
    "mimetypes" => ":attribute tiene que ser un archivo de tipo: :values.",
    "mimes" => ":attribute tiene que ser un archivo de tipo: :values.",
    "file" => ":attribute tiene que ser un archivo.",
    "integer" => ":attribute tiene que ser un número entero.",
    "numeric" => ":attribute tiene que ser un número.",
    "json" => ":attribute tiene que ser una cadena JSON válida.",
    "string" => ":attribute tiene que ser una cadena.",
    "ip" => ":attribute tiene que ser una dirección IP válida.",
    "ipv4" => ":attribute tiene que ser una dirección IPv4 válida.",
    "ipv6" => ":attribute tiene que ser una dirección IPv6 válida.",
    "email" => ":attribute tiene que ser una dirección de correo válida.",
    "before" => ":attribute tiene que ser una fecha anterior a :date.",
    "before_or_equal" => ":attribute tiene que ser una fecha anterior o igual a :date.",
    "after" => ":attribute tiene que ser una fecha posterior a :date.",
    "after_or_equal" => ":attribute tiene que ser una fecha posterior o igual a :date.",
    "image" => ":attribute tiene que ser una imagen.",
    "array" => ":attribute tiene que ser una matriz.",
    "timezone" => ":attribute tiene que ser una zona válida.",
    "same" => ":attribute y :other deben coincidir.",
    "different" => ":attribute y :other deben ser diferentes.",
    "unique" => ":attribute ya se ha utilizado.",
    "present" => "El campo :attribute de existir.",
    "boolean" => "El campo :attribute debe ser verdadero o falso.",
    "required_unless" => "El campo :attribute es necesario a no ser que :other esté en :values.",
    "required_if" => "El campo :attribute es necesario cuando :other es :value.",
    "required_with_all" => "El campo :attribute es necesario cuando :values exista.",
    "required_with" => "El campo :attribute es necesario cuando :values exista.",
    "required_without" => "El campo :attribute es necesario cuando :values no exista.",
    "required_without_all" => "El campo :attribute es necesario cuando ningún :values exista.",
    "required" => "El campo :attribute es necesario.",
    "in_array" => "El campo :attribute no existe en :other.",
    "filled" => "El campo :attribute tiene que tener un valor.",
    "distinct" => "El campo :attribute tiene un valor duplicado.",
    "regex" => "El formato de :attribute no es válido.",
    "url" => "El formato de :attribute no es válido.",
    "not_regex" => "El formato de :attribute no es válido.",
    "uploaded" => "Fallo al subir :attribute.",
    "confirmed" => "La confirmación de :attribute no coincide.",
    "custom" => [
        "attribute-name" => [
            "rule-name" => "custom-message"
        ]
    ],
    "max" => [
        "numeric" => ":attribute no puede ser superior a :max.",
        "file" => ":attribute no puede ser superior a :max kilobytes.",
        "string" => ":attribute no puede ser superior a :max caracteres.",
        "array" => ":attribute no puede contener más de :max objetos."
    ],
    "between" => [
        "numeric" => ":attribute tiene que estar entre :min y :max.",
        "file" => ":attribute tiene que estar entre :min y :max kilobytes.",
        "string" => ":attribute tiene que estar entre :min y :max caracteres.",
        "array" => ":attribute tiene que estar entre :min y :max objetos."
    ],
    "min" => [
        "numeric" => ":attribute tiene que ser al menos :min.",
        "file" => ":attribute tiene que ser al menos :min kilobytes.",
        "string" => ":attribute tiene que contener al menos :min caracteres.",
        "array" => ":attribute tiene que tener al menos :min objetos."
    ],
    "gt" => [
        "numeric" => ":attribute tiene que ser superior a :value.",
        "file" => ":attribute tiene que ser superior a :value kilobytes.",
        "string" => ":attribute tiene que ser superior a :value caracteres.",
        "array" => ":attribute tiene que tener más de :value objetos."
    ],
    "gte" => [
        "numeric" => ":attribute tiene que ser superior o igual a :value.",
        "file" => ":attribute tiene que ser superior o igual a :value kilobytes.",
        "string" => ":attribute tiene que ser superior o igual a :value characters.",
        "array" => ":attribute tiene que tener :value objetos o más."
    ],
    "size" => [
        "numeric" => "El tamaño de :attribute tiene que ser :size.",
        "file" => "El tamaño de :attribute tiene que ser de :size kilobytes.",
        "string" => "El tamaño de :attribute tiene que ser de :size caractéres.",
        "array" => ":attribute tiene que contener :size objetos."
    ],
    "lt" => [
        "numeric" => ":attribute tiene que ser inferior a :value.",
        "file" => ":attribute tiene que ser inferior a :value kilobytes.",
        "string" => ":attribute tiene que ser inferior a :value caracteres.",
        "array" => ":attribute tiene que tener menos de :value objetos."
    ],
    "lte" => [
        "numeric" => ":attribute tiene que ser inferior o igual a :value.",
        "file" => ":attribute tiene que ser inferior o igual a :value kilobytes.",
        "string" => ":attribute tiene que ser inferior o igual a :value caracteres.",
        "array" => ":attribute no puede tener más de :value objetos."
    ]
];