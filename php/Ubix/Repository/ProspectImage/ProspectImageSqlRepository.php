<?php

declare(strict_types=1);

namespace Ubix\Repository\ProspectImage;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlRepository\ProspectImageOptions;
use Ubix\Enum\ProspectImage\ProspectImageType;
use Ubix\Model\ProspectImage;
use Ubix\Repository\ProspectImage\ProspectImageReaderInterface as ProspectImageReader;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading fan prospect application data
 *
 * @see \Ubix\Tests\Repository\ProspectImage\ProspectImageSqlRepositoryTest PHPUnit test case
 */
final class ProspectImageSqlRepository implements ProspectImageReader
{
    /**
     * Constructor
     *
     * @param Logger     $logger     Logger
     * @param SqlService $sqlService SQL service
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private SqlService $sqlService,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getByProspectId(int $prospectId): array
    {
        return $this->query(new ProspectImageOptions(
            prospectId: $prospectId,
        ));
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param ProspectImageOptions $options DTO of options to generate the query
     *
     * @return ProspectImage[] An array of application objects
     */
    private function query(ProspectImageOptions $options): array
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT
                    pi.md5_hash,
                    pi.prospect_id,
                    pi.upload_date,
                    pi.file_name,
                    pi.file_size,
                    pi.file_type,
                    pi.image_type,
                    pi.binary_image_data,
                    pi.build_status,
                    pi.purge_requested_datetime
                FROM STUDIOS.Prospects_Images pi
                WHERE 1 = 1';

        if ($options->prospectId !== null) {
            $sql .= ' AND pi.prospect_id = :prospectId';

            $parameters['prospectId'] = $options->prospectId;
        }

        //
        //  Query the database and return the result(s) as object(s)
        //
        $objects = [];

        /**
         * @var array{
         *     md5_hash: ?string,
         *     prospect_id: ?int,
         *     upload_date: ?string,
         *     file_name: ?string,
         *     file_size: ?float,
         *     file_type: ?string,
         *     image_type: ?string,
         *     binary_image_data: ?string,
         *     build_status: ?int,
         *     purge_requested_datetime: ?string,
         * } $row
         */
        foreach ($this->sqlService->getRows($sql, $parameters) as $row) {
            $objects[] = new ProspectImage(
                md5Hash:                $row['md5_hash'],
                prospectId:             $row['prospect_id'],
                uploadDate:             $row['upload_date'] !== null ? new DateTime($row['upload_date']) : null,
                fileName:               $row['file_name'],
                fileSize:               $row['file_size'],
                fileType:               $row['file_type'],
                imageType:              match (strtolower($row['image_type'] ?? '')) {
                    'photo_id_front' => ProspectImageType::PHOTO_ID_FRONT,
                    'photo_id'       => ProspectImageType::PHOTO_ID,
                    'photo_id_back'  => ProspectImageType::PHOTO_ID_BACK,
                    'photo_id_face'  => ProspectImageType::PHOTO_ID_FACE,
                    'images'         => ProspectImageType::IMAGES,
                    default          => null,
                },
                binaryImageData:        $row['binary_image_data'],
                buildStatus:            $row['build_status'],
                purgeRequestedDatetime: $row['purge_requested_datetime'] !== null ? new DateTime($row['purge_requested_datetime']) : null,
            );
        }

        return $objects;
    }
}
