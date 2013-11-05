<?php

class Worldview_Tagger_Helper_Pos_Stanford extends Worldview_Tagger_Helper_Pos_Data
{
    const TAGGED_TERMS_SEPARATOR = ' ';
    const TAGGED_TERM_POS_SEPARATOR = '_';
    const TAGGED_TERM_POS_TERM_INDEX = 0;
    const TAGGED_TERM_POS_POS_INDEX = 1;

    protected $_text_blob_file = 'stanford.txt';
    protected $_text_blob_separator_tagged_result = '@_IN #_# $_$ %_NN @_IN #_# $_$ %_NN ._.';

    public function getPrincipalPosStrings()
    {
        return array('NNP');
    }

    public function parseFile($absolute_file_path, $blob_order_ids = null)
    {
        $parsed_text_sentences = $this->executeFileParse($absolute_file_path);

        $pos_terms_array = array();
        $blob_counter = 0;
        $blob_identifier = is_array($blob_order_ids) ? $blob_order_ids[$blob_counter] : $blob_counter;
        $text_blob_separator_tagged_result = $this->getBlobSeparatorTaggedResult();

        foreach ($parsed_text_sentences as $tagged_sentence)
        {
            $last_sentence_in_blob = false;
            // Check to see if this sentence only contained the blob separator result
            if ($tagged_sentence == $text_blob_separator_tagged_result)
            {
                $last_sentence_in_blob = true;
            }
            else
            {
                // Check to see if the blob separator result was affixed to the end of the this sentence
                $blob_separator_result_pos = strpos ($tagged_sentence, $text_blob_separator_tagged_result);
                if ($blob_separator_result_pos !== false)
                {
                    $last_sentence_in_blob = true;
                    // If so, extract the tagged separator result from the sentence
                    $tagged_sentence = str_replace($text_blob_separator_tagged_result, '', $tagged_sentence);
                }

                // $tagged_sentence is of form A_DT passenger_NN plane_NN has_VBZ crashed_VBN shortly_RB after_IN
                $tagged_terms = explode(self::TAGGED_TERMS_SEPARATOR, $tagged_sentence);
                foreach ($tagged_terms as $tagged_term)
                {
                    $term_and_pos = explode(self::TAGGED_TERM_POS_SEPARATOR, $tagged_term);
                    $term = $term_and_pos[self::TAGGED_TERM_POS_TERM_INDEX];
                    $pos = $term_and_pos[self::TAGGED_TERM_POS_POS_INDEX];

                    if (!isset($pos_terms_array[$blob_identifier]))
                    {
                        $pos_terms_array[$blob_identifier] = array();
                    }
                    if (!isset($pos_terms_array[$blob_identifier][$pos]))
                    {
                        $pos_terms_array[$blob_identifier][$pos] = array();
                    }
                    if (!isset($pos_terms_array[$blob_identifier][$pos][$term]))
                    {
                        $pos_terms_array[$blob_identifier][$pos][$term] = 1;
                    }
                    else
                    {
                        $pos_terms_array[$blob_identifier][$pos][$term] = $pos_terms_array[$blob_identifier][$pos][$term] + 1;
                    }
                }
            }

            if ($last_sentence_in_blob)
            {
                $blob_counter++;
                $blob_identifier = is_array($blob_order_ids) ? $blob_order_ids[$blob_counter] : $blob_counter;
            }
        }

        return $pos_terms_array;
    }

    public function executeFileParse($absolute_file_path)
    {
        $output = "";
        exec( 'java -mx300m -cp "POS/stanford-postagger.jar;" edu.stanford.nlp.tagger.maxent.MaxentTagger -model POS/models/wsj-0-18-left3words-distsim.tagger -textFile ' . $absolute_file_path , $output );

        return $output;
    }

    public function getBlobSeparatorTaggedResult()
    {
        return $this->_text_blob_separator_tagged_result;
    }
}

