<?php
/**
 * File containing the ezcGraphAxisBoxedLabelRenderer class
 *
 * @package Graph
 * @version //autogentag//
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
        2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Renders axis labels centered between two axis steps like normally used for 
 * bar charts.
 *
 * @package Graph
 * @mainclass
 */
class ezcGraphAxisBoxedLabelRenderer extends ezcGraphAxisLabelRenderer
{
    /**
     * Store step array for later coordinate modifications
     * 
     * @var array( ezcGraphStep )
     */
    protected $steps;

    /**
     * Store direction for later coordinate modifications
     * 
     * @var ezcGraphVector
     */
    protected $direction;

    /**
     * Store coordinate width modifier for later coordinate modifications
     * 
     * @var float
     */
    protected $widthModifier;
    
    /**
     * Constructor
     * 
     * @param array $options Default option array
     * @return void
     * @ignore
     */
    public function __construct( array $options = array() )
    {
        parent::__construct( $options );
        $this->properties['outerStep'] = true;
    }

    /**
     * Render Axis labels
     *
     * Render labels for an axis.
     *
     * @param ezcGraphRenderer $renderer Renderer used to draw the chart
     * @param ezcGraphBoundings $boundings Boundings of the axis
     * @param ezcGraphCoordinate $start Axis starting point
     * @param ezcGraphCoordinate $end Axis ending point
     * @param ezcGraphChartElementAxis $axis Axis instance
     * @return void
     */
    public function renderLabels(
        ezcGraphRenderer $renderer,
        ezcGraphBoundings $boundings,
        ezcGraphCoordinate $start,
        ezcGraphCoordinate $end,
        ezcGraphChartElementAxis $axis )
    {
        // receive rendering parameters from axis
        $steps = $axis->getSteps();
        $this->steps = $steps;

        $axisBoundings = new ezcGraphBoundings(
            $start->x, $start->y,
            $end->x, $end->y
        );

        // Determine normalized axis direction
        $this->direction = new ezcGraphVector(
            $start->x - $end->x,
            $start->y - $end->y
        );
        $this->direction->unify();
        
        if ( $this->outerGrid )
        {
            $gridBoundings = $boundings;
        }
        else
        {
            $gridBoundings = new ezcGraphBoundings(
                $boundings->x0 + $renderer->xAxisSpace,
                $boundings->y0 + $renderer->yAxisSpace,
                $boundings->x1 - $renderer->xAxisSpace,
                $boundings->y1 - $renderer->yAxisSpace
            );
        }

        // Determine additional required axis space by boxes
        $firstStep = reset( $steps );
        $lastStep = end( $steps );

        $this->widthModifier = 1 + $firstStep->width / 2 + $lastStep->width / 2;

        // Draw steps and grid
        foreach ( $steps as $nr => $step )
        {
            $position = new ezcGraphCoordinate(
                $start->x + ( $end->x - $start->x ) * ( $step->position + $step->width ) / $this->widthModifier,
                $start->y + ( $end->y - $start->y ) * ( $step->position + $step->width ) / $this->widthModifier
            );
    
            $stepWidth = $step->width / $this->widthModifier;

            $stepSize = new ezcGraphCoordinate(
                $axisBoundings->width * $stepWidth,
                $axisBoundings->height * $stepWidth
            );

            // Calculate label boundings
            if ( abs( $this->direction->x ) > abs( $this->direction->y ) )
            {
                $labelBoundings = new ezcGraphBoundings(
                    $position->x - $stepSize->x + $this->labelPadding,
                    $position->y + $this->labelPadding,
                    $position->x + - $this->labelPadding,
                    $position->y + $renderer->yAxisSpace - $this->labelPadding
                );

                $alignement = ezcGraph::CENTER | ezcGraph::TOP;
            }
            else
            {
                $labelBoundings = new ezcGraphBoundings(
                    $position->x - $renderer->xAxisSpace + $this->labelPadding,
                    $position->y - $stepSize->y + $this->labelPadding,
                    $position->x - $this->labelPadding,
                    $position->y - $this->labelPadding
                );

                $alignement = ezcGraph::MIDDLE | ezcGraph::RIGHT;
            }

            $renderer->drawText( $labelBoundings, $step->label, $alignement );

            // major grid
            if ( $axis->majorGrid )
            {
                $this->drawGrid( 
                    $renderer, 
                    $gridBoundings, 
                    $position,
                    $stepSize,
                    $axis->majorGrid
                );
            }
            
            // major step
            $this->drawStep( 
                $renderer, 
                $position,
                $this->direction, 
                $axis->position, 
                $this->majorStepSize, 
                $axis->border
            );
        }
    }
    
    /**
     * Modify chart data position
     *
     * Optionally additionally modify the coodinate of a data point
     * 
     * @param ezcGraphCoordinate $coordinate Data point coordinate
     * @return ezcGraphCoordinate Modified coordinate
     */
    public function modifyChartDataPosition( ezcGraphCoordinate $coordinate )
    {
        $firstStep = reset( $this->steps );
        $offset = $firstStep->width / 2 / $this->widthModifier;

        return new ezcGraphCoordinate(
            $coordinate->x * abs( $this->direction->y ) +
                ( $coordinate->x / $this->widthModifier + $offset ) * abs( $this->direction->x ),
            $coordinate->y * abs( $this->direction->x ) +
                ( $coordinate->y / $this->widthModifier + $offset ) * abs( $this->direction->y )
        );
    }
}
?>
