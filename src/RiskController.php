<?php
require __DIR__ . '/BaseController.php';
require __DIR__ . '/model/RiskScore.php';
require __DIR__ . '/model/RiskScoreToRiskProfileMapper.php';
require __DIR__ . '/model/UserProfile.php';
require __DIR__ . '/model/House.php';
require __DIR__ . '/model/Vehicle.php';

class RiskController extends BaseController {
    public function calculate(array $parameters) {
        $this->validate($this->getRules(), $parameters);

        $risk_score = (new RiskScore())->calculate($this->buildUserProfile($parameters));
        $risk_profile = RiskScoreToRiskProfileMapper::map($risk_score);

        $this->sendSuccessResponse($risk_profile);
    }

    private function getRules(): array {
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

    private function buildUserProfile($parameters): UserProfile {
        return new UserProfile(
            $parameters['age'],
            $parameters['dependents'],
            $parameters['income'],
            new House($parameters['house']['ownership_status']),
            $parameters['marital_status'] === 'married',
            $parameters['risk_questions'],
            new Vehicle($parameters['vehicle']['year'])
        );
    }
}