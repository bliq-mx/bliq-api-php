<?php

declare(strict_types=1);

namespace Bliq\Api\StampResults;

use DateTime;

class CancelCfdiResultV3
{
    /**
     * @var array
     */
    private $estatus;

    /**
     * @var array|null
     */
    private $cancelacion;


    public function __construct(array $response)
    {
        $this->estatus = $response['data']['estatus'];
        $this->cancelacion = $response['data']['cancelacion'] ?: null;
    }

    /**
     * Posibles valores:
     * - cancelado
     * - en_proceso
     * - no_cancelable
     * - cancelable_con_aceptacion
     * - cancelable_sin_aceptacion
     */
    public function estatusCodigo(): string
    {
        return $this->estatus['codigo'];
    }

    /**
     * Indica si el CFDI ha sido cancelado.
     */
    public function estatusCancelado(): bool
    {
        return $this->estatus['cancelado'];
    }

    /**
     * Indica si es posible solicitar la cancelación.
     */
    public function estatusSolicitar(): bool
    {
        return $this->estatus['solicitar'];
    }

    /**
     * Si la cancelación fue exitosa, regresa la fecha de cancelación.
     * @return DateTime|null
     */
    public function cancelacionFecha()
    {
        $value = $this->cancelacion['fecha'] ?: null;
        if (null === $value) {
            return null;
        }
        return new DateTime($value);
    }

    /**
     * Si la cancelación fue exitosa, regresa el XML del acuse.
     * @return string|null
     */
    public function cancelacionXml()
    {
        return $this->cancelacion['xml'] ?: null;
    }

    /**
     * Si la cancelación fue exitosa, regresa el UUID del CFDI cancelado.
     * @return string|null
     */
    public function cancelacionUuid()
    {
        return $this->cancelacion['uuid'] ?: null;
    }

    /**
     * Si se realizó la petición de cancelación, regresa el estatus de la cancelación.
     * @return string|null
     */
    public function cancelacionEstatus()
    {
        return $this->cancelacion['estatus_cancelacion'] ?: null;
    }

    /**
     * Si se realizó la petición de cancelación, regresa el estatus del documento.
     * @return string|null
     */
    public function cancelacionEstatusUuid()
    {
        return $this->cancelacion['estatus_uuid'] ?: null;
    }
}
