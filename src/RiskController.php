<?php
require __DIR__ . '/BaseController.php';

class RiskController extends BaseController {
    public function calculate($parameters) {
        $this->validate($this->getRules(), $parameters);
    }

    public function getRules(): array {
        return [
            'age' => ['required' => true, 'format' => 'integer'],
            'dependents' => ['required' => true, 'format' => 'integer'],
            'income' => ['required' => true, 'format' => 'integer'],
            'marital_status' => ['required' => true, 'contains' => ['single', 'married']],
            'risk_questions' => [
                'required' => true,
                'format' => [
                    'array' => [
                        'format' => 'boolean',
                        'size' => 3
                    ]
                ]
            ],
            'house' => [
                'required' => false,
                'format' => [
                    'array' => [
                        'format' => 'string',
                        'size' => 1,
                        'keys' => ['ownership_status'],
                        'contains' => ['owned', 'mortgaged']
                    ]
                ]
            ],
            'vehicle' => [
                'required' => false,
                'format' => [
                    'array' => [
                        'format' => 'integer',
                        'size' => 1,
                        'keys' => ['year']
                    ]
                ]
            ]
        ];
    }
}