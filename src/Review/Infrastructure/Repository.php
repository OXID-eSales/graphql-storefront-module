<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Review\Infrastructure;

use OxidEsales\Eshop\Application\Model\Article as EshopArticleModel;
use OxidEsales\Eshop\Application\Model\Rating as EshopRatingModel;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Review\DataType\Review as ReviewDataType;
use PDO;

final class Repository
{
    /** @var QueryBuilderFactoryInterface */
    private $queryBuilderFactory;

    public function __construct(
        QueryBuilderFactoryInterface $queryBuilderFactory
    ) {
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    /**
     * @return true
     */
    public function delete(ReviewDataType $review): bool
    {
        try {
            $rating = $this->ratingForReview($review);
            $rating->delete();
        } catch (NotFound $e) {
            // Just move on if there's no rating
        }
        $review->getEshopModel()->delete();

        return true;
    }

    public function saveRating(ReviewDataType $review): bool
    {
        /** @var EshopRatingModel */
        $eshopRatingModel = oxNew(EshopRatingModel::class);

        $eshopRatingModel->assign(
            [
                'oxuserid' => $review->getReviewerId(),
                'oxobjectid' => $review->getObjectId(),
                'oxrating' => $review->getRating(),
                'oxtype' => 'oxarticle',
            ]
        );
        $eshopRatingModel->save();

        /** @var EshopArticleModel */
        $eshopArticleModel = oxNew(EshopArticleModel::class);
        $eshopArticleModel->load($review->getObjectId());
        $eshopArticleModel->addToRatingAverage($review->getRating());

        return true;
    }

    public function doesReviewExist(string $userId, ReviewDataType $review): bool
    {
        $reviewAndRatingList = $review
            ->getEshopModel()
            ->getReviewAndRatingListByUserId(
                $userId
            );

        foreach ($reviewAndRatingList as $reviewAndRating) {
            if ($reviewAndRating->getObjectId() == $review->getObjectId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sadly there is no relation between oxratings and oxreviews table but the
     * oxuserid, oxobject and oxrating values beeing identical ...
     */
    private function ratingForReview(ReviewDataType $review): EshopRatingModel
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);

        $queryBuilder->select('ratings.*')
            ->from($tableViewNameGenerator->getViewName('oxratings'), 'ratings')
            ->where('oxuserid = :userid')
            ->andWhere('oxobjectid = :object')
            ->andWhere('oxrating = :rating')
            ->setParameters(
                [
                    'userid' => $review->getReviewerId(),
                    'object' => $review->getObjectId(),
                    'rating' => $review->getRating(),
                ]
            )
            ->setMaxResults(1);
        /** @var \Doctrine\DBAL\Statement $result */
        $result = $queryBuilder->execute();

        if ($result->rowCount() !== 1) {
            throw new NotFound();
        }

        /** @var EshopRatingModel */
        $model = oxNew(EshopRatingModel::class);
        $model->assign(
            $result->fetch(
                PDO::FETCH_ASSOC
            )
        );

        return $model;
    }
}
